<?php

namespace App\Entity;

use App\Repository\JugadoresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JugadoresRepository::class)]
class Jugadores
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?int $estadisticas = null;

    #[ORM\ManyToOne(inversedBy: 'jugadores')]
    private ?Equipos $equipo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getEstadisticas(): ?int
    {
        return $this->estadisticas;
    }

    public function setEstadisticas(int $estadisticas): static
    {
        $this->estadisticas = $estadisticas;

        return $this;
    }

    public function getEquipo(): ?Equipos
    {
        return $this->equipo;
    }

    public function setEquipo(?Equipos $equipo): static
    {
        $this->equipo = $equipo;

        return $this;
    }
}
