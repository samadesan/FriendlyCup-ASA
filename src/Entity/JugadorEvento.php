<?php

namespace App\Entity;

use App\Repository\JugadorEventoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JugadorEventoRepository::class)]
class JugadorEvento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'evento')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Jugadores $jugador = null;

    #[ORM\ManyToOne(inversedBy: 'jugadorEventos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PuntuajeEvento $evento = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJugador(): ?Jugadores
    {
        return $this->jugador;
    }

    public function setJugador(?Jugadores $jugador): static
    {
        $this->jugador = $jugador;

        return $this;
    }

    public function getEvento(): ?PuntuajeEvento
    {
        return $this->evento;
    }

    public function setEvento(?PuntuajeEvento $evento): static
    {
        $this->evento = $evento;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }
}
