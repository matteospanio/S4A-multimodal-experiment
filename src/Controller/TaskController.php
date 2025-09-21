<?php

namespace App\Controller;

use App\Entity\Trial\FlavorToMusicTrial;
use App\Entity\Trial\MusicToFlavorTrial;
use App\Entity\Trial\Trial;
use App\Form\F2MTrialType;
use App\Form\M2FTrialType;
use App\Service\DataCollector;
use App\Service\StimuliManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

final class TaskController extends AbstractController
{
    public function __construct(
        private readonly StimuliManager $stimuliManager,
        private readonly DataCollector $dataCollector,
    )
    {
    }

    #[Route('/task/{type}', name: 'app_task', requirements: ['type' => '\d+'], methods: ['GET'])]
    public function index(int $type = 1): Response
    {
        $task = $this->getTask($type);
        $trial = $this->stimuliManager->getNextTrial($task);

        $form = match($task) {
            Trial::MUSICS2SMELL => $this->createForm(M2FTrialType::class, $trial),
            Trial::SMELLS2MUSIC => $this->createForm(F2MTrialType::class, $trial),
            default => throw $this->createNotFoundException('Invalid task type'),
        };

        return $this->render('task/index.html.twig', [
            'task_type' => $type,
            'trial' => $trial,
            'form' => $form,
        ]);
    }

    #[Route('/task/{type}/{id:trial}/submit', name: 'app_task_submit', requirements: ['type' => '\d+'], methods: ['POST'])]
    public function submit(Request $request, int $type, Trial $trial): Response
    {
        $task = $this->getTask($type);

        $form = match($task) {
            Trial::MUSICS2SMELL => $this->createForm(M2FTrialType::class, $trial),
            Trial::SMELLS2MUSIC => $this->createForm(F2MTrialType::class, $trial),
            default => throw $this->createNotFoundException('Invalid task type'),
        };

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $choice = $form->get('choice')->getData();
            $this->dataCollector->recordVote($choice, $trial);
            return $this->redirectToRoute('app_task_results', ['type' => $type, 'id' => $trial->getId()]);
        }

        return $this->redirectToRoute('app_task', ['type' => $type]);
    }

    #[Route('/task/{type}/{id:trial}/results', name: 'app_task_results', requirements: ['type' => '\d+'], methods: ['GET'])]
    public function showResults(int $type, Trial $trial, ChartBuilderInterface $chartBuilder): Response
    {
        $task = $this->getTask($type);
        $choice = $trial->getChoice();

        if ($task === Trial::MUSICS2SMELL) {
            assert($trial instanceof MusicToFlavorTrial);
            $success = $choice === $trial->getFlavor();
            // prepare data for musics to smell results
        } else {
            assert($trial instanceof FlavorToMusicTrial);
            $success = $choice === $trial->getSong();
            // prepare data for smells to music results
        }

        return $this->render('task/results.html.twig', []);
    }

    private function getTask(int $type): string
    {
        return match ($type) {
            1 => Trial::MUSICS2SMELL,
            2 => Trial::SMELLS2MUSIC,
            default => throw $this->createNotFoundException('Invalid task type'),
        };
    }
}
