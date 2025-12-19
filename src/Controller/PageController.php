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
    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        $user = $this->getUser();
         if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $torneos=$user->getTorneos();
        $seguidos=$user->getSeguidos();
        return $this->render('page/index.html.twig', [
            'torneos'=>$torneos,
            'seguidos'=>$seguidos,
            'controller_name' => 'PageController',
        ]);
    }
    #[Route('/torneo/update/{id}', name: 'modificartorneo')]
    public function updatetorneo(ManagerRegistry $doctrine, Request $request, $id): Response 
    {
    $entityManager = $doctrine->getManager();
    $repositorio = $doctrine->getRepository(Torneo::class);
    $torneo = $repositorio->find($id);
    if ($torneo) {
        $formulario = $this->createForm(TorneoFormType::class, $torneo);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $entityManager->flush(); 
            return $this->redirectToRoute('torneo', ["id" => $torneo->getId()]);
        }
        return $this->render('page/editar-torneo.html.twig', [
            'formulario' => $formulario->createView(),
            'torneo' => $torneo
        ]);
    }
    return $this->redirectToRoute('inicio');
    }
    #[Route('/torneo/delete/{id}', name: 'torneoeliminado')]
    public function delete(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Torneo::class);
        $torneo = $repositorio->find($id);
        if ($torneo) {
            $entityManager->remove($torneo);
            $entityManager->flush();
        }
        return $this->redirectToRoute('inicio');
    }
    #[Route('/creaciontorneo', name: 'crear-torneo')]
    public function creacionligas(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $entityManager = $doctrine ->getManager();
        $torneo = new Torneo();
        $formulario = $this->createForm(TorneoFormType::class, $torneo);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $torneo->setOrganizador($user);
            $torneo->setSeguidores(0);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($torneo);
            $entityManager->flush();
            return $this->redirectToRoute('torneo', ["id" => $torneo->getId()]);        }
        return $this->render('page/crear-torneo.html.twig', [
            'controller_name' => 'PageController',
            'formulario' => $formulario->createView()
        ]);
    }
    #[Route('/torneo/{id}/crearequipo', name: 'crear-equipo')]
    public function crearEquipo(int $id,ManagerRegistry $doctrine,Request $request): Response {
        $entityManager = $doctrine->getManager();
        $torneoRepo = $doctrine->getRepository(Torneo::class);
        $torneo = $torneoRepo->find($id);
        if (!$torneo) {
            return $this->redirectToRoute('inicio');
        }
        $equipo = new Equipos();
        $equipo->setTorneo($torneo);
        $equipo->setPuntos(0);
        $formulario = $this->createForm(EquiposFormType::class, $equipo);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $entityManager->persist($equipo);
            $entityManager->flush();
            return $this->redirectToRoute('equipo', [
                'id' => $equipo->getId()
            ]);
        }
        return $this->render('page/crear-equipo.html.twig', [
            'formulario' => $formulario->createView(),
            'torneo' => $torneo
        ]);
    }
    #[Route('/torneo/{id}', name: 'torneo')]
    public function ligas(int $id,ManagerRegistry $doctrine): Response
    {
        $repositorio = $doctrine->getRepository(Torneo::class);
        $torneo=$repositorio->find($id);
        $admin=$torneo->getOrganizador();
        return $this->render('page/torneos.html.twig', [
            'torneo' => $torneo,
            'admin'  => $admin,
        ]);
    }
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
            'equipo' => $equipo
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
        return $this->redirectToRoute('inicio');
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
    #[Route('/jugador/update/{id}', name: 'modificarjugador')]
    public function updateJugador(ManagerRegistry $doctrine,Request $request,int $id): Response {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Jugadores::class);
        $jugador = $repositorio->find($id);
        if (!$jugador) {
            return $this->redirectToRoute('inicio');
        }
        $formulario = $this->createForm(JugadorFormType::class, $jugador);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('jugador', [
                'id' => $jugador->getId()
            ]);
        }
        return $this->render('page/editar-jugador.html.twig', [
            'formulario' => $formulario->createView(),
            'jugador' => $jugador
        ]);
    }
    #[Route('/jugador/delete/{id}', name: 'jugadoreliminado')]
    public function deleteJugador(ManagerRegistry $doctrine,int $id): Response {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Jugadores::class);
        $jugador = $repositorio->find($id);
        if ($jugador) {
            $entityManager->remove($jugador);
            $entityManager->flush();
        }
        return $this->redirectToRoute('inicio');
    }
    #[Route('/equipo/{id}/crearjugador', name: 'crear-jugador')]
    public function crearJugador(int $id,ManagerRegistry $doctrine,Request $request): Response {
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
        return $this->render('page/crear-jugador.html.twig', [
            'formulario' => $formulario->createView(),
            'equipo' => $equipo
        ]);
    }

    #[Route('/jugador/{id}', name: 'jugador')]
    public function verJugador(int $id,ManagerRegistry $doctrine): Response {
        $repositorio = $doctrine->getRepository(Jugadores::class);
        $jugador = $repositorio->find($id);
        if (!$jugador) {
            return $this->redirectToRoute('inicio');
        }
        return $this->render('page/jugador.html.twig', [
            'jugador' => $jugador,
            'equipo' => $jugador->getEquipo(),
            'estadisticas' => $jugador->getEstadisticas(),
        ]);
    }

    #[Route('/fantasy', name: 'fantasy')]
    public function fantasy(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    #[Route('/creacionfantasy', name: 'creacionfantasy')]
    public function creacionfantasy(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    
}