<?php

use App\Entity\Torneo;
use App\Entity\Equipos;
use App\Entity\Disputas;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DisputasController extends AbstractController
{
    #[Route('torneo/{id}/creardisputa', name: 'creardisputa', methods: ['POST'])]
    public function crearJugador(int $id,ManagerRegistry $doctrine,Request $request): JsonResponse {
        $entityManager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $equipoRepo = $entityManager->getRepository(Equipos::class);
        $torneoRepo = $doctrine->getRepository(Torneo::class);
        $torneo = $torneoRepo->find($id);
        $disputa=new Disputas();
        $disputa->setResultado('0-0');
        $disputa->setEquipo1(($data['equipo1Id']));
        $disputa->setEquipo2(($data['equipo2Id']));
        $disputa->setGanador(null);
        $entityManager->persist($disputa);
        $entityManager->flush();
        return new JsonResponse($disputa->getId());
    }
}