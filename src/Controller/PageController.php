<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    #[Route('/torneo', name: 'torneo')]
    public function ligas(): Response
    {
        return $this->render('page/torneos.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    #[Route('/creaciontorneo', name: 'creaciontorneo')]
    public function creacionligas(): Response
    {
        return $this->render('page/crear-torneo.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    #[Route('/fantasy', name: 'fantasy')]
    public function fantasy(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    #[Route('/creacionfantasy', name: 'creacionfantasy')]
    public function creacionfantasy(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    
}