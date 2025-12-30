<?php

namespace App\Controller;

use App\Entity\Torneo;
use App\Entity\LigaFantasy;
use App\Entity\EquipoFantasy;
use App\Form\LigaFantasyFormType;
use App\Repository\JugadoresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\EquipoFantasyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/fantasy')]
final class FantasyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private EquipoFantasyRepository $equipoRepo,
        private JugadoresRepository $jugadoresRepo
    ) {}

    #[Route('/crear-fantasy/{id}', name: 'fantasycrear')]
    public function crearDesdeTorneo(Request $request, Torneo $torneo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $liga = new LigaFantasy();
        $liga->setTorneo($torneo);
        $liga->setPuntuaje(0);
        $liga->setAdministrador($user);

        $form = $this->createForm(LigaFantasyFormType::class, $liga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($liga);
            $miEquipo = new EquipoFantasy();
            $miEquipo->setEntrenador($user);
            $miEquipo->setLigafantasy($liga);
            $miEquipo->setPresupuesto($form->get('presupuestoInicial')->getData());
            $miEquipo->setPuntos(0);
            $this->em->persist($miEquipo);
            $this->em->flush();

            return $this->redirectToRoute('fantasy_liga', ['id' => $liga->getId()]);
        }

        return $this->render('fantasy/crear_liga.html.twig', [
            'form' => $form->createView(),
            'torneo' => $torneo
        ]);
    }

    #[Route('/liga/delete/{id}', name: 'fantasyterminar')]
    public function deleteFantasy(ManagerRegistry $doctrine, int $id): Response 
    {
        $entityManager = $doctrine->getManager();
        $liga = $doctrine->getRepository(LigaFantasy::class)->find($id);

        if ($liga) {
            $entityManager->remove($liga);
            $entityManager->flush();
        }

        return $this->redirectToRoute('inicio');
    }

    #[Route('/api/equipo/{id}/presupuesto', name: 'api_update_presupuesto', methods: ['POST'])]
    public function actualizarPresupuesto(EquipoFantasy $equipo,Request $request,EntityManagerInterface $em):JsonResponse{
        $data = json_decode($request->getContent(), true);
        $equipo->setPresupuesto($data['presupuesto']);
        $em->flush();
        return $this->json($equipo, 200, [], ['groups' => 'equipo:read']);
    }

    #[Route('/liga/{id}', name: 'fantasy_liga')]
    public function index(LigaFantasy $liga): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $miEquipo = $this->equipoRepo->findOneBy([
            'entrenador' => $user,
            'ligafantasy' => $liga
        ]);

        $misJugadoresObjs = $miEquipo->getTitulares()->toArray();

        $todosLosJugadores = $this->jugadoresRepo->findAll();

        $idsOcupados = [];
        foreach ($liga->getEquipoFantasies() as $equipoFantasy) {
            foreach ($equipoFantasy->getTitulares() as $jugador) {
                $idsOcupados[] = $jugador->getId();
            }
        }

        $mercado = array_filter($todosLosJugadores, fn($jugador) => !in_array($jugador->getId(), $idsOcupados));

        $equiposEnLiga = $this->equipoRepo->findBy(['ligafantasy' => $liga]);

        return $this->render('fantasy/index.html.twig', [
            'liga' => $liga,
            'equipo' => $miEquipo,
            'misJugadores' => $misJugadoresObjs,
            'mercado' => $mercado,
            'equiposEnLiga' => $equiposEnLiga
        ]);
    }
}
