<?php

namespace App\Entity;

use App\Repository\JugadoresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private int $estadisticas = 0;

    #[ORM\Column]
    private int $valorDeMercado = 0;

    #[ORM\ManyToOne(inversedBy: 'jugadores')]
    private ?Equipos $equipo = null;

    #[ORM\ManyToMany(targetEntity: EquipoFantasy::class, mappedBy: 'titulares',)]
    private Collection $equipoFantasies;

    /**
     * @var Collection<int, JugadorEvento>
     */
    #[ORM\OneToMany(targetEntity: JugadorEvento::class, mappedBy: 'jugador')]
    private Collection $evento;


    public function __construct()
    {
        $this->equipoFantasies = new ArrayCollection();
        $this->evento = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getEstadisticas(): int
    {
        return $this->estadisticas;
    }

    public function setEstadisticas(int $estadisticas): self
    {
        $this->estadisticas = $estadisticas;
        return $this;
    }

    public function getValorDeMercado(): int
    {
        return $this->valorDeMercado;
    }

    public function setValorDeMercado(int $valorDeMercado): self
    {
        $this->valorDeMercado = $valorDeMercado;
        return $this;
    }

    public function getEquipo(): ?Equipos
    {
        return $this->equipo;
    }

    public function setEquipo(?Equipos $equipo): self
    {
        $this->equipo = $equipo;
        return $this;
    }

    /**
     * @return Collection<int, EquipoFantasy>
     */
    public function getEquipoFantasies(): Collection
    {
        return $this->equipoFantasies;
    }

    /**
     * @return Collection<int, JugadorEvento>
     */
    public function getEvento(): Collection
    {
        return $this->evento;
    }

    public function addEvento(JugadorEvento $evento): static
    {
        if (!$this->evento->contains($evento)) {
            $this->evento->add($evento);
            $evento->setJugador($this);
        }

        return $this;
    }

    public function removeEvento(JugadorEvento $evento): static
    {
        if ($this->evento->removeElement($evento)) {
            // set the owning side to null (unless already changed)
            if ($evento->getJugador() === $this) {
                $evento->setJugador(null);
            }
        }

        return $this;
    }
}