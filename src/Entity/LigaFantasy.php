<?php

namespace App\Entity;

use App\Repository\LigaFantasyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigaFantasyRepository::class)]
class LigaFantasy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?int $minimoJugadores = 11;

    #[ORM\ManyToOne(inversedBy: 'ligaFantasies')]
    private ?Torneo $torneo = null;

    #[ORM\Column]
    private ?int $puntuaje = null;

    /**
     * @var Collection<int, EquipoFantasy>
     */
    #[ORM\OneToMany(targetEntity: EquipoFantasy::class, mappedBy: 'ligafantasy',cascade: ['remove'])]
    private Collection $equipoFantasies;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $administrador = null;

    #[ORM\Column(type:"integer", nullable:true)]
    private $cierredemercado;


    public function __construct()
    {
        $this->equipoFantasies = new ArrayCollection();
    }

    public function getCierredemercado(): ?int
    {
        return $this->cierredemercado;
    }

    public function setCierredemercado(int $cierredemercado): self
    {
        $this->cierredemercado = $cierredemercado;
        return $this;
    }

    public function getAdministrador(): ?User
    {
        return $this->administrador;
    }

    public function setAdministrador(?User $administrador): static
    {
        $this->administrador = $administrador;

        return $this;
    }

    public function getMinimoJugadores(): ?int
    {
        return $this->minimoJugadores;
    }

    public function setMinimoJugadores(int $minimoJugadores): static
    {
        $this->minimoJugadores = $minimoJugadores;
        return $this;
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

    public function getPuntuaje(): ?int
    {
        return $this->puntuaje;
    }

    public function setPuntuaje(int $puntuaje): static
    {
        $this->puntuaje = $puntuaje;

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
            $equipoFantasy->setLigafantasy($this);
        }

        return $this;
    }

    public function removeEquipoFantasy(EquipoFantasy $equipoFantasy): static
    {
        if ($this->equipoFantasies->removeElement($equipoFantasy)) {
            // set the owning side to null (unless already changed)
            if ($equipoFantasy->getLigafantasy() === $this) {
                $equipoFantasy->setLigafantasy(null);
            }
        }

        return $this;
    }
    public function filtrarJugadoresLibres(array $todosLosJugadores): array
    {
        $libres = [];
        foreach ($todosLosJugadores as $jugador) {
            $estaOcupado = false;
            foreach ($this->getEquipoFantasies() as $equipo) {
                if ($equipo->tieneJugador($jugador->getId())) {
                    $estaOcupado = true;
                    break; 
                }
            }
            if (!$estaOcupado) {
                $libres[] = $jugador;
            }
        }
        return $libres;
    }
    public function estaJugadorOcupado(EquipoFantasy $miEquipo, int $jugadorId): bool
    {
        $rivales = $miEquipo->getLigafantasy()->getEquipoFantasies();
        foreach ($rivales as $rival) {
            if ($rival->getId() === $miEquipo->getId()) continue; 
            if ($rival->tieneJugador($jugadorId)) {
                return true;
            }
        }
        return false;
    }
}
