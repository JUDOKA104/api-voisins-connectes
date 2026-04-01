<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['annonce:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['annonce:read'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['annonce:read'])]
    private ?string $photoProfil = null;

    /**
     * @var Collection<int, Annonce>
     */
    #[ORM\OneToMany(targetEntity: Annonce::class, mappedBy: 'createur', orphanRemoval: true)]
    private Collection $annoncesCreees;

    /**
     * @var Collection<int, Annonce>
     */
    #[ORM\OneToMany(targetEntity: Annonce::class, mappedBy: 'helper')]
    private Collection $annoncesAidees;

    #[ORM\Column]
    private ?bool $isBanned = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastActivityAt = null;

    public function __construct()
    {
        $this->annoncesCreees = new ArrayCollection();
        $this->annoncesAidees = new ArrayCollection();
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
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(string $photoProfil): static
    {
        $this->photoProfil = $photoProfil;
        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnoncesCreees(): Collection
    {
        return $this->annoncesCreees;
    }

    public function addAnnoncesCreee(Annonce $annoncesCreee): static
    {
        if (!$this->annoncesCreees->contains($annoncesCreee)) {
            $this->annoncesCreees->add($annoncesCreee);
            $annoncesCreee->setCreateur($this);
        }
        return $this;
    }

    public function removeAnnoncesCreee(Annonce $annoncesCreee): static
    {
        if ($this->annoncesCreees->removeElement($annoncesCreee)) {
            if ($annoncesCreee->getCreateur() === $this) {
                $annoncesCreee->setCreateur(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnoncesAidees(): Collection
    {
        return $this->annoncesAidees;
    }

    public function addAnnoncesAidee(Annonce $annoncesAidee): static
    {
        if (!$this->annoncesAidees->contains($annoncesAidee)) {
            $this->annoncesAidees->add($annoncesAidee);
            $annoncesAidee->setHelper($this);
        }
        return $this;
    }

    public function removeAnnoncesAidee(Annonce $annoncesAidee): static
    {
        if ($this->annoncesAidees->removeElement($annoncesAidee)) {
            if ($annoncesAidee->getHelper() === $this) {
                $annoncesAidee->setHelper(null);
            }
        }
        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): static
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getLastActivityAt(): ?\DateTimeImmutable
    {
        return $this->lastActivityAt;
    }

    public function setLastActivityAt(?\DateTimeImmutable $lastActivityAt): static
    {
        $this->lastActivityAt = $lastActivityAt;

        return $this;
    }
}
