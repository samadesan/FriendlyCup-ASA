<?php

namespace App\Controller;

use App\Entity\Disputas;
use App\Repository\DisputasRepository;
use App\Repository\EquiposRepository;
use App\Repository\TorneoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DisputasController extends AbstractController
{
    #[Route('/disputas', name: 'app_disputas')]
    public function index(): Response
    {
        return $this->render('disputas/index.html.twig', [
            'controller_name' => 'DisputasController',
        ]);
    }
    #[Route('/disputas/crear', name: 'creardisputas', methods: ['POST'])]
    public function crearDisputas(Request $request, EntityManagerInterface $entityManager,EquiposRepository $equiposRepository,TorneoRepository $torneoRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $disputa= new Disputas();
        $disputa->setResultado('0-0');
        $equipo1 = $equiposRepository->find($data['equipo1_id']);
        $equipo2 = $equiposRepository->find($data['equipo2_id']);
        $torneo  = $torneoRepository->find($data['torneo_id']) ?? 'aaa';
        if (!$equipo1) return new JsonResponse(['error' => 'El Equipo 1 no existe'], 404);
        if (!$equipo2) return new JsonResponse(['error' => 'El Equipo 2 no existe'], 404);
        if (!$torneo)  return new JsonResponse(['error' => 'El Torneo no existe'], 404);
        $disputa->setEquipo1($equipo1);
        $disputa->setEquipo2($equipo2);
        $disputa->setTorneo($torneo);
        $entityManager->persist($disputa);
        $entityManager->flush();
        return new JsonResponse($disputa->getId());
    }
     #[Route('/disputas/modificar', name: 'modificardisputas')]
     public function modificarDisputas(){

     }
}
