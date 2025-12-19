<?php

namespace App\Entity;

use App\Repository\EquiposRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquiposRepository::class)]
class Equipos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(inversedBy: 'equipos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Torneo $torneo = null;

    /**
     * @var Collection<int, Jugadores>
     */
    #[ORM\OneToMany(targetEntity: Jugadores::class, mappedBy: 'equipo', cascade: ['persist', 'remove'])]
    private Collection $jugadores;

    #[ORM\OneToOne(mappedBy: 'equipo1', cascade: ['persist', 'remove'])]
    private ?Disputas $disputas = null;

    #[ORM\Column]
    private ?int $puntos = 0;

    public function __construct()
    {
        $this->jugadores = new ArrayCollection();
        $this->disputas = new ArrayCollection();
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

    public function getTorneo(): ?Torneo
    {
        return $this->torneo;
    }

    public function setTorneo(?Torneo $torneo): static
    {
        $this->torneo = $torneo;

        return $this;
    }

    /**
     * @return Collection<int, Jugadores>
     */
    public function getJugadores(): Collection
    {
        return $this->jugadores;
    }

    public function addJugadores(Jugadores $jugadore): static
    {
        if (!$this->jugadores->contains($jugadore)) {
            $this->jugadores->add($jugadore);
            $jugadore->setEquipo($this);
        }

        return $this;
    }

    public function removeJugadore(Jugadores $jugadore): static
    {
        if ($this->jugadores->removeElement($jugadore)) {
            // set the owning side to null (unless already changed)
            if ($jugadore->getEquipo() === $this) {
                $jugadore->setEquipo(null);
            }
        }

        return $this;
    }

    public function getDisputas(): ?Disputas
    {
        return $this->disputas;
    }

    public function setDisputas(Disputas $disputas): static
    {
        // set the owning side of the relation if necessary
        if ($disputas->getEquipo1() !== $this) {
            $disputas->setEquipo1($this);
        }

        $this->disputas = $disputas;

        return $this;
    }

    public function addDisputa(Disputas $disputa): static
    {
        if (!$this->disputas->contains($disputa)) {
            $this->disputas->add($disputa);
            $disputa->setEquipo2($this);
        }

        return $this;
    }

    public function removeDisputa(Disputas $disputa): static
    {
        if ($this->disputas->removeElement($disputa)) {
            // set the owning side to null (unless already changed)
            if ($disputa->getEquipo2() === $this) {
                $disputa->setEquipo2(null);
            }
        }

        return $this;
    }

    public function getPuntos(): ?int
    {
        return $this->puntos;
    }

    public function setPuntos(int $puntos): static
    {
        $this->puntos = $puntos;

        return $this;
    }
}
