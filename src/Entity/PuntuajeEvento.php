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

    /**
     * @var \Doctrine\Common\Collections\Collection<int, JugadorEvento>
     */
    #[ORM\OneToMany(targetEntity: JugadorEvento::class, mappedBy: 'evento')]
    private \Doctrine\Common\Collections\Collection $jugadorEventos;

    public function __construct()
    {
        $this->jugadorEventos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return \Doctrine\Common\Collections\Collection<int, JugadorEvento>
     */
    public function getJugadorEventos(): \Doctrine\Common\Collections\Collection
    {
        return $this->jugadorEventos;
    }

    public function addJugadorEvento(JugadorEvento $jugadorEvento): static
    {
        if (!$this->jugadorEventos->contains($jugadorEvento)) {
            $this->jugadorEventos->add($jugadorEvento);
            $jugadorEvento->setEvento($this);
        }

        return $this;
    }

    public function removeJugadorEvento(JugadorEvento $jugadorEvento): static
    {
        if ($this->jugadorEventos->removeElement($jugadorEvento)) {
            // set the owning side to null (unless already changed)
            if ($jugadorEvento->getEvento() === $this) {
                $jugadorEvento->setEvento(null);
            }
        }

        return $this;
    }
}
