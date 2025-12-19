<?php

namespace App\Controller;

use App\Entity\Torneo;
use App\Entity\Equipos;
use App\Entity\Jugadores;
use App\Form\TorneoFormType;
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
            $torneo = $formulario->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($torneo);
            $entityManager->flush();
            return $this->redirectToRoute('torneo', ["id" => $torneo->getId()]);        }
        return $this->render('page/crear-torneo.html.twig', [
            'controller_name' => 'PageController',
            'formulario' => $formulario->createView()
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
    
    #[Route('/equipo/guardar', name: 'equipo_guardar', methods: ['POST'])]
    public function guardar(Request $request, EntityManagerInterface $em,TorneoRepository $torneoRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $equipo = new Equipos();
        $equipo->setNombre($data['nombre']);
        $equipo->setPuntos(0);

        $torneoId = $data['torneoId'] ?? null;
        $torneo = $torneoRepo->find($torneoId);
        $equipo->setTorneo($torneo);

        foreach ($data['jugadores'] as $nombreJugador) {
            $jugador = new Jugadores();
            $jugador->setNombre($nombreJugador);
            $jugador->setEstadisticas(0);
            $equipo->addJugadores($jugador);
        }
        $em->persist($equipo);
        $em->flush();
        return new JsonResponse(['status' => 'success', 'id' => $equipo->getId()]);
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