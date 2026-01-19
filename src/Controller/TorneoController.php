<?php

namespace App\Controller;

use App\Entity\Torneo;
use App\Entity\Equipos;
use App\Form\EquipoFormType;
use App\Form\TorneoFormType;
use App\Repository\TorneoRepository;
use App\Repository\JugadoresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TorneoController extends AbstractController
{
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
            'equipos' => $torneo->getEquipos(),
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
    #[Route('/torneos', name: 'lista_torneos')]
    public function index(TorneoRepository $repo, Request $request): Response
    {
        $termino = $request->query->get('q'); 

        if ($termino) {
            $torneos = $repo->buscarPorNombre($termino);
        } else {
            $torneos = $repo->findAll();
        }

        return $this->render('page/buscar/index.html.twig', [
            'torneos' => $torneos,
            'termino' => $termino
        ]);
    }
    #[Route('/torneo/{id}/crearequipo', name: 'crearequipo')]
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
        $formulario = $this->createForm(EquipoFormType::class, $equipo);
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
    #[Route('/torneo/{id}/seguir', name: 'torneo_seguir', methods: ['POST'])]
    public function seguir(Torneo $torneo,EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($user->getSeguidos()->contains($torneo)) {
            $user->removeSeguido($torneo);
            $accion = 'desseguir';
        }else{
            $user->addSeguido($torneo);
            $accion = 'seguir';
        }
        $em->flush();
        return $this->json([
        'accion' => $accion,
        'totalSeguidores' => $torneo->getSeguidores()->count()
        ]);
    }
    #[Route('/torneo/{id}/resumen', name: 'torneo_resumen')]
    public function resumen(Torneo $torneo): Response
    {
        $admin=$torneo->getOrganizador();
        $disputas=$torneo->getDisputas();

        return $this->render('page/torneo/resumen.html.twig', [
            'torneo' => $torneo,
            'admin'  => $admin,
            'disputas'  => $disputas,
        ]);
    }
    #[Route('/torneo/{id}/clasificacion', name: 'torneo_clasificacion')]
    public function clasificacion(Torneo $torneo): Response
    {
        return $this->render('page/torneo/clasificacion.html.twig', [
            'equipos' => $torneo->getEquipos()
        ]);
    }
    #[Route('/torneo/{id}/estadisticas', name: 'torneo_estadisticas')]
    public function estadisticas(Torneo $torneo): Response
    {
        $jugadores =[];
        foreach ($torneo->getEquipos() as $equipo) {
            foreach ($equipo->getJugadores() as $jugador) {
                $jugadores[$jugador->getId()] = $jugador;
            }
        }
        return $this->render('page/torneo/estadisticas.html.twig', [
            'jugadores' => $jugadores,
            'torneo' => $torneo
        ]);
    }
    #[Route('/torneo/{id}', name: 'torneo')]
    public function ligas(int $id,ManagerRegistry $doctrine): Response
    {
        $repositorio = $doctrine->getRepository(Torneo::class);
        $torneo=$repositorio->find($id);
        $admin=$torneo->getOrganizador();
        $user = $this->getUser();
        $yaSigue = false;
        $disputas = $torneo->getDisputas();
        if ($user) {
            $yaSigue = $user->getSeguidos()->contains($torneo);
        }
        return $this->render('page/torneos.html.twig', [
            'torneo' => $torneo,
            'admin'  => $admin,
            'yasigue' => $yaSigue,
            'disputas'=> $disputas
        ]);
    }
}
