<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PuntuajeEventoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Collection;

#[ORM\Entity(repositoryClass: PuntuajeEventoRepository::class)]
class PuntuajeEvento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'puntajeEventos')]
    private ?Torneo $torneo = null;

    #[ORM\Column]
    private int $puntos;

    #[ORM\Column(length: 255)]
    private string $evento;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\ManyToMany(targetEntity: Jugadores::class, mappedBy: 'puntajeEventos')]
    private Collection $jugadores;

    public function __construct(){
        $this->jugadores = new ArrayCollection();
    }

    public function getJugadores(): Collection
    {
        return $this->jugadores;
    }

    public function addJugador(Jugadores $jugador): self
    {
        if (!$this->jugadores->contains($jugador)) {
            $this->jugadores->add($jugador);
        }
        return $this;
    }

    public function removeJugador(Jugadores $jugador): self
    {
        $this->jugadores->removeElement($jugador);
        return $this;
    }

    public function getTorneo(): ?Torneo
    {
        return $this->torneo;
    }

    public function setTorneo(?Torneo $torneo): self
    {
        $this->torneo = $torneo;
        return $this;
    }

    public function getPuntos(): int
    {
        return $this->puntos;
    }

    public function setPuntos(int $puntos): self
    {
        $this->puntos = $puntos;
        return $this;
    }

    public function getEvento(): string
    {
        return $this->evento;
    }

    public function setEvento(string $evento): self
    {
        $this->evento = $evento;
        return $this;
    }
}
