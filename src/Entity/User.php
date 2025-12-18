<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Torneo>
     */
    #[ORM\OneToMany(targetEntity: Torneo::class, mappedBy: 'organizador')]
    private Collection $torneos;

    /**
     * @var Collection<int, EquipoFantasy>
     */
    #[ORM\OneToMany(targetEntity: EquipoFantasy::class, mappedBy: 'entrenador')]
    private Collection $equipoFantasies;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    public function __construct()
    {
        $this->torneos = new ArrayCollection();
        $this->equipoFantasies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    /**
     * @return Collection<int, Torneo>
     */
    public function getTorneos(): Collection
    {
        return $this->torneos;
    }

    public function addTorneo(Torneo $torneo): static
    {
        if (!$this->torneos->contains($torneo)) {
            $this->torneos->add($torneo);
            $torneo->setOrganizador($this);
        }

        return $this;
    }

    public function removeTorneo(Torneo $torneo): static
    {
        if ($this->torneos->removeElement($torneo)) {
            // set the owning side to null (unless already changed)
            if ($torneo->getOrganizador() === $this) {
                $torneo->setOrganizador(null);
            }
        }

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
            $equipoFantasy->setEntrenador($this);
        }

        return $this;
    }

    public function removeEquipoFantasy(EquipoFantasy $equipoFantasy): static
    {
        if ($this->equipoFantasies->removeElement($equipoFantasy)) {
            // set the owning side to null (unless already changed)
            if ($equipoFantasy->getEntrenador() === $this) {
                $equipoFantasy->setEntrenador(null);
            }
        }

        return $this;
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

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }
}
