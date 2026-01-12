<?php

namespace App\Controller;

use App\Entity\Jugadores;
use App\Form\JugadorFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class JugadorController extends AbstractController
{
    #[Route('/jugador/update/{id}', name: 'modificarjugador')]
    public function updateJugador(ManagerRegistry $doctrine,Request $request,int $id): Response {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Jugadores::class);
        $jugador = $repositorio->find($id);
        $equipo = $jugador->getEquipo();
        $torneo = $equipo->getTorneo();
        if (!$jugador) {
            return $this->redirectToRoute('inicio');
        }
        $formulario = $this->createForm(JugadorFormType::class, $jugador);
        $formulario->handleRequest($request);
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('equipo', [
                'id' => $equipo->getId()
            ]);
        }
        return $this->render('page/editar-jugador.html.twig', [
            'formulario' => $formulario->createView(),
            'jugador' => $jugador,
            'torneo'     => $torneo,
            
        ]);
    }
    #[Route('/jugador/delete/{id}', name: 'jugadoreliminado')]
    public function deleteJugador(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Jugadores::class);
        $jugador = $repositorio->find($id);
        if (!$jugador) {
            return $this->redirectToRoute('inicio');
        }
        $equiposFantasy = $jugador->getEquipoFantasies()->toArray();
        foreach ($equiposFantasy as $equipoFantasy) {
             $equipoFantasy->getTitulares()->removeElement($jugador);
             $entityManager->persist($equipoFantasy);
        }
        $equipo = $jugador->getEquipo();
        $equipoId = $equipo ? $equipo->getId() : null;
        $entityManager->flush(); 
        $entityManager->remove($jugador);
        $entityManager->flush(); 
        return $this->redirectToRoute('equipo', [
            'id' => $equipoId
        ]);
    }
}