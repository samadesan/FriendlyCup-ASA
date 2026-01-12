<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DisputasController extends AbstractController
{
    #[Route('/disputas', name: 'app_disputas')]
    public function index(): Response
    {
        return $this->render('disputas/index.html.twig', [
            'controller_name' => 'DisputasController',
        ]);
    }
}
