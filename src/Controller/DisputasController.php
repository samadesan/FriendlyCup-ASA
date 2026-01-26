<?php

namespace App\Controller;

use App\Entity\Disputas;
use App\Repository\DisputasRepository;
use App\Repository\EquiposRepository;
use App\Repository\TorneoRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Torneo;
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
    public function crearDisputas(Request $request, EntityManagerInterface $entityManager, EquiposRepository $equiposRepository, TorneoRepository $torneoRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $disputa = new Disputas();
        $disputa->setResultado('n-n');
        $equipo1 = $equiposRepository->find($data['equipo1_id']);
        $equipo2 = $equiposRepository->find($data['equipo2_id']);
        $torneo = $torneoRepository->find($data['torneo_id']) ?? 'aaa';
        $disputa->setEquipo1($equipo1);
        $disputa->setEquipo2($equipo2);
        $disputa->setTorneo($torneo);
        $entityManager->persist($disputa);
        $entityManager->flush();
        return new JsonResponse($disputa->getId());
    }
    #[Route('/disputas/{id}/modificar', name: 'modificardisputas', methods: ['POST'])]
    public function modificarDisputas(Request $request, EntityManagerInterface $entityManager, DisputasRepository $disputasRepository, EquiposRepository $equiposRepository, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $disputa = $disputasRepository->find($id);
        $disputa->setResultado($data['resultado']);
        if ($data['ganador_id']) {
            $equipoGanador = $equiposRepository->find($data['ganador_id']);
            $disputa->setGanador($equipoGanador);
        } else {
            $disputa->setGanador(null);
        }
        $this->calculodepuntos($disputa->getTorneo(), $entityManager);
        $entityManager->flush();
        return new JsonResponse($disputa->getId());
    }
    #[Route('/disputas/{id}/eliminar', name: 'eliminardisputas')]
    public function eliminarDisputas(Request $request, EntityManagerInterface $entityManager, DisputasRepository $disputasRepository, int $id): Response
    {
        $disputa = $disputasRepository->find($id);
        $torneoId = $disputa->getTorneo()->getId();
        $entityManager->remove($disputa);
        $entityManager->flush();
        $this->calculodepuntos($disputa->getTorneo(), $entityManager);
        return $this->redirectToRoute('torneo', ['id' => $torneoId]);
    }
    private function calculodepuntos(Torneo $torneo, EntityManagerInterface $entityManager)
    {
        $equipos = $torneo->getEquipos();
        foreach ($equipos as $equipo) {
            $equipo->setPuntos(0);
            $disputas = $equipo->getTodasLasDisputas();
            foreach ($disputas as $disputa) {
                if ($disputa->getGanador() == $equipo) {
                    $equipo->setPuntos($equipo->getPuntos() + 3);
                } elseif ($disputa->getResultado() != 'n-n' && $disputa->getGanador() == null) {
                    $equipo->setPuntos($equipo->getPuntos() + 1);
                }
            }
            $entityManager->persist($equipo);
        }
        $entityManager->flush();
    }
}