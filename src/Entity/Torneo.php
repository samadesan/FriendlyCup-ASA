<?php

namespace App\Entity;

use App\Repository\TorneoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TorneoRepository::class)]
class Torneo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $tipo = null;

    #[ORM\ManyToOne(inversedBy: 'torneos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizador = null;

    /**
     * @var Collection<int, Equipos>
     */
    #[ORM\OneToMany(targetEntity: Equipos::class, mappedBy: 'torneo')]
    private Collection $equipos;

    /**
     * @var Collection<int, Disputas>
     */
    #[ORM\OneToMany(targetEntity: Disputas::class, mappedBy: 'torneo')]
    private Collection $disputas;

    public function __construct()
    {
        $this->equipos = new ArrayCollection();
        $this->disputas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getOrganizador(): ?User
    {
        return $this->organizador;
    }

    public function setOrganizador(?User $organizador): static
    {
        $this->organizador = $organizador;

        return $this;
    }

    /**
     * @return Collection<int, Equipos>
     */
    public function getEquipos(): Collection
    {
        return $this->equipos;
    }

    public function addEquipo(Equipos $equipo): static
    {
        if (!$this->equipos->contains($equipo)) {
            $this->equipos->add($equipo);
            $equipo->setTorneo($this);
        }

        return $this;
    }

    public function removeEquipo(Equipos $equipo): static
    {
        if ($this->equipos->removeElement($equipo)) {
            // set the owning side to null (unless already changed)
            if ($equipo->getTorneo() === $this) {
                $equipo->setTorneo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Disputas>
     */
    public function getDisputas(): Collection
    {
        return $this->disputas;
    }

    public function addDisputa(Disputas $disputa): static
    {
        if (!$this->disputas->contains($disputa)) {
            $this->disputas->add($disputa);
            $disputa->setTorneo($this);
        }

        return $this;
    }

    public function removeDisputa(Disputas $disputa): static
    {
        if ($this->disputas->removeElement($disputa)) {
            // set the owning side to null (unless already changed)
            if ($disputa->getTorneo() === $this) {
                $disputa->setTorneo(null);
            }
        }

        return $this;
    }
}
