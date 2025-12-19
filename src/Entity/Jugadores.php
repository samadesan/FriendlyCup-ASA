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
    private ?int $estadisticas = 0;

    #[ORM\ManyToOne(inversedBy: 'jugadores')]
    private ?Equipos $equipo = null;

    /**
     * @var Collection<int, EquipoFantasy>
     */
    #[ORM\OneToMany(targetEntity: EquipoFantasy::class, mappedBy: 'jugadores')]
    private Collection $equipoFantasies;

    /**
     * @var Collection<int, PuntuajeEvento>
     */
    #[ORM\OneToMany(targetEntity: PuntuajeEvento::class, mappedBy: 'jugador')]
    private Collection $puntuajeEventos;

    public function __construct()
    {
        $this->equipoFantasies = new ArrayCollection();
        $this->puntuajeEventos = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, EquipoFantasy>
     */
    public function getEquipoFantasies(): Collection
    {
        return $this->equipoFantasies;
    }

    public function addEquipoFantasy(EquipoFantasy $equipoFantasy): static
    {
        if (!$this->equipoFantasies->contains($equipoFantasy)) {
            $this->equipoFantasies->add($equipoFantasy);
            $equipoFantasy->setJugadores($this);
        }

        return $this;
    }

    public function removeEquipoFantasy(EquipoFantasy $equipoFantasy): static
    {
        if ($this->equipoFantasies->removeElement($equipoFantasy)) {
            // set the owning side to null (unless already changed)
            if ($equipoFantasy->getJugadores() === $this) {
                $equipoFantasy->setJugadores(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PuntuajeEvento>
     */
    public function getPuntuajeEventos(): Collection
    {
        return $this->puntuajeEventos;
    }

    public function addPuntuajeEvento(PuntuajeEvento $puntuajeEvento): static
    {
        if (!$this->puntuajeEventos->contains($puntuajeEvento)) {
            $this->puntuajeEventos->add($puntuajeEvento);
            $puntuajeEvento->setJugador($this);
        }

        return $this;
    }

    public function removePuntuajeEvento(PuntuajeEvento $puntuajeEvento): static
    {
        if ($this->puntuajeEventos->removeElement($puntuajeEvento)) {
            // set the owning side to null (unless already changed)
            if ($puntuajeEvento->getJugador() === $this) {
                $puntuajeEvento->setJugador(null);
            }
        }

        return $this;
    }
}
