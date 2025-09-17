<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/task/{type}', name: 'app_task', requirements: ['type' => '\d+'])]
    public function index(int $type = 1): Response
    {
        // Validate task type
        if (!in_array($type, [1, 2])) {
            throw $this->createNotFoundException('Invalid task type');
        }

        return $this->render('task/index.html.twig', [
            'task_type' => $type,
            'task_name' => $type === 1 ? 'Audio-Perfume Matching' : 'Perfume-Audio Association',
        ]);
    }
}
