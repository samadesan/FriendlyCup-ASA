<?php

namespace App\Controller;

use App\Entity\Equipos;
use App\Entity\Jugadores;
use App\Entity\JugadorEvento;
use App\Form\EquipoFormType;
use App\Form\JugadorFormType;
use App\Repository\EventoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class EquipoController extends AbstractController
{
    #[Route('/equipo/update/{id}', name: 'modificarequipo')]
    public function updateEquipo(ManagerRegistry $doctrine,Request $request,int $id): Response {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Equipos::class);
        $equipo = $repositorio->find($id);
        if (!$equipo) {
            return $this->redirectToRoute('inicio');
        }
        $formulario = $this->createForm(EquipoFormType::class, $equipo);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('equipo', [
                'id' => $equipo->getId()
            ]);
        }
        return $this->render('page/editar-equipo.html.twig', [
            'formulario' => $formulario->createView(),
            'equipo' => $equipo,
            'torneo'=>$equipo->getTorneo(),
            'jugadores'=>$equipo->getJugadores()

        ]);
    }
    #[Route('/equipo/delete/{id}', name: 'equipoeliminado')]
    public function deleteEquipo(ManagerRegistry $doctrine,int $id): Response {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Equipos::class);
        $equipo = $repositorio->find($id);
        if ($equipo) {
            $entityManager->remove($equipo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('torneo', [
        'id' => $equipo->getTorneo()->getId()
    ]);
    }
    #[Route('/equipo/{id}/crearjugador', name: 'crearjugador')]
    public function crearJugador(int $id,ManagerRegistry $doctrine,Request $request,EventoRepository $evento): Response {
        $entityManager = $doctrine->getManager();
        $equipoRepo = $doctrine->getRepository(Equipos::class);
        $equipo = $equipoRepo->find($id);

        if (!$equipo) {
            return $this->redirectToRoute('inicio');
        }
        $jugador = new Jugadores();
        $jugador->setEquipo($equipo);
        $jugador->setEstadisticas(0);
        $formulario = $this->createForm(JugadorFormType::class, $jugador);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $entityManager->persist($jugador);
            $entityManager->flush();
            return $this->redirectToRoute('equipo', [
                'id' => $equipo->getId()
            ]);
        }
        $eventos = $evento->findAll();
        foreach ($eventos as $evento) {
            $eventojugador=new JugadorEvento();
            $eventojugador->setEvento($evento);
            $eventojugador->setCantidad(0);
            $eventojugador->setJugador($jugador);
            $entityManager->persist($eventojugador);
        }
        $entityManager->flush();
        return $this->render('page/crear-jugador.html.twig', [
            'formulario' => $formulario->createView(),
            'equipo' => $equipo
        ]);
    }
    #[Route('/equipo/{id}', name: 'equipo')]
    public function verEquipo(int $id,ManagerRegistry $doctrine): Response {
        $repositorio = $doctrine->getRepository(Equipos::class);
        $equipo = $repositorio->find($id);
        if (!$equipo) {
            return $this->redirectToRoute('inicio');
        }
        return $this->render('page/equipo.html.twig', [
            'equipo' => $equipo,
            'torneo' => $equipo->getTorneo(),
            'jugadores' => $equipo->getJugadores(),
            'puntos' => $equipo->getPuntos()
        ]);
    }
}
