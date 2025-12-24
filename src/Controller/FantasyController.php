<?php

namespace App\Controller;

use App\Entity\Torneo;
use App\Entity\LigaFantasy;
use App\Entity\EquipoFantasy;
use App\Form\LigaFantasyFormType;
use App\Repository\JugadoresRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EquipoFantasyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/fantasy')]
final class FantasyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,private EquipoFantasyRepository $equipoRepo,private JugadoresRepository $jugadoresRepo) {}
    
    
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
        $form = $this->createForm(LigaFantasyFormType::class, $liga);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($liga);
            $miEquipo = new EquipoFantasy();
            $miEquipo->setEntrenador($this->getUser());
            $miEquipo->setLigafantasy($liga);
            $miEquipo->setPresupuesto(100000000); 
            $miEquipo->setPuntos(0);
            $miEquipo->setDatosAlineacion([
                'titulares' => [],
                'suplentes' => [],
                'coste_compra' => []
            ]);
            $this->em->persist($miEquipo);
            $this->em->flush();
            return $this->redirectToRoute('fantasy_liga',
            ['id' => $liga->getId()]);
        }
        return $this->render('fantasy/crear_liga.html.twig', [
            'form' => $form->createView(),
            'torneo' => $torneo
        ]);
    }
    #[Route('/liga/{id}', name: 'fantasy_liga')]
    public function index(LigaFantasy $liga): Response
    {
        $user = $this->getUser();
        $miEquipo = $this->equipoRepo->findOneBy([
            'entrenador' => $user,
            'ligafantasy' => $liga
        ]);
        $idsMisJugadores = array_merge($miEquipo->getDatosAlineacion()['titulares'], $miEquipo->getDatosAlineacion()['suplentes']);
        $misJugadoresObjs = empty($idsMisJugadores) ? [] : $this->jugadoresRepo->findBy(['id' => $idsMisJugadores]);
        $todosLosJugadores = $this->jugadoresRepo->findAll();
        $mercado = $miEquipo->getLigafantasy()->filtrarJugadoresLibres($todosLosJugadores);
        $equiposEnLiga = $this->equipoRepo->findBy([
        'ligafantasy' => $liga
        ]);
        return $this->render('fantasy/index.html.twig', [
            'liga' => $liga, 
            'equipo' => $miEquipo,
            'misJugadores' => $misJugadoresObjs,
            'mercado' => $mercado,
            'datosAlineacion' => $miEquipo->getDatosAlineacion(),
            'equiposEnLiga' => $equiposEnLiga
        ]);
    }
}