<?php

namespace App\Controller;

use App\Entity\Torneo;
use App\Entity\Equipos;
use App\Entity\Jugadores;
use App\Form\EquipoFormType;
use App\Form\TorneoFormType;
use App\Form\JugadorFormType;
use App\Repository\TorneoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PageController extends AbstractController
{
    #[Route('/FC', name: 'inicio')]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user) {
            $torneos=$user->getTorneos();
            $seguidos=$user->getSeguidos();
            $fantasies=$user->getEquipoFantasies();
        }else{
            return $this->redirectToRoute('app_login');
        }

        return $this->render('page/index.html.twig', [
            'torneos'=>$torneos,
            'seguidos'=>$seguidos,
            'controller_name' => 'PageController',
            'fantasies'=>$fantasies
        ]);
    }

    #[Route('/', name: 'presentation')]
    public function presentation(){
        return $this->render('page/indice.html.twig');
    }
}