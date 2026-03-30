<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
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

    public function __construct()
    {
        $this->annoncesCreees = new ArrayCollection();
        $this->annoncesAidees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            // set the owning side to null (unless already changed)
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
            // set the owning side to null (unless already changed)
            if ($annoncesAidee->getHelper() === $this) {
                $annoncesAidee->setHelper(null);
            }
        }

        return $this;
    }
}
