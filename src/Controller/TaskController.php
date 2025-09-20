<?php

namespace App\Controller;

use App\Entity\Trial\Trial;
use App\Form\F2MTrialType;
use App\Form\M2FTrialType;
use App\Service\DataCollector;
use App\Service\StimuliManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    public function __construct(
        private readonly StimuliManager $stimuliManager,
        private readonly DataCollector $dataCollector,
    )
    {
    }

    #[Route('/task/{type}', name: 'app_task', requirements: ['type' => '\d+'], methods: ['GET', 'POST'])]
    public function index(Request $request, int $type = 1): Response
    {
        $task = match ($type) {
            1 => Trial::MUSICS2SMELL,
            2 => Trial::SMELLS2MUSIC,
            default => throw $this->createNotFoundException('Invalid task type'),
        };
        $trial = $this->stimuliManager->getNextTrial($task);

        $form = match($task) {
            Trial::MUSICS2SMELL => $this->createForm(M2FTrialType::class, $trial),
            Trial::SMELLS2MUSIC => $this->createForm(F2MTrialType::class, $trial),
            default => throw $this->createNotFoundException('Invalid task type'),
        };

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $choice = $form->get('choice')->getData();
            $this->dataCollector->recordVote($choice, $trial);
            return $this->redirectToRoute('app_task', ['type' => $type]);
        }

        return $this->render('task/index.html.twig', [
            'task_type' => $type,
            'trial' => $trial,
            'form' => $form,
            'task_name' => $type === 1 ? 'Audio-Perfume Matching' : 'Perfume-Audio Association',
        ]);
    }
}
