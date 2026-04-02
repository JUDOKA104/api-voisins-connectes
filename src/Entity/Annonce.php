<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['annonce:read'])] // Ajouté pour pouvoir identifier l'annonce sur Angular
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['annonce:read'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['annonce:read'])] // Ajouté pour voir le texte de l'annonce !
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['annonce:read'])]
    private ?string $statut = null;

    #[ORM\Column]
    #[Groups(['annonce:read'])] // Ajouté pour voir quand elle a été postée
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['annonce:read'])]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'annoncesCreees')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['annonce:read'])]
    private ?User $createur = null;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'annonce', orphanRemoval: true)]
    #[Groups(['annonce:read'])]
    private Collection $commentaires;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'annoncesAidees')]
    #[ORM\JoinTable(name: 'annonce_helpers')]
    #[Groups(['annonce:read'])]
    private Collection $helpers;

    #[ORM\Column(options: ["default" => false])]
    #[Groups(['annonce:read'])]
    private ?bool $estRemunere = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['annonce:read'])]
    private ?int $maxHelpers = null;

    public function __construct()
    {
        $this->helpers = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->dateCreation = new \DateTimeImmutable();
        $this->statut = 'En attente';
    }

    /**
     * @return Collection<int, User>
     */
    public function getHelpers(): Collection
    {
        return $this->helpers;
    }

    public function addHelper(User $helper): static
    {
        if (!$this->helpers->contains($helper)) {
            $this->helpers->add($helper);
        }
        return $this;
    }

    public function removeHelper(User $helper): static
    {
        $this->helpers->removeElement($helper);
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getCreateur(): ?User
    {
        return $this->createur;
    }

    public function setCreateur(?User $createur): static
    {
        $this->createur = $createur;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function isEstRemunere(): ?bool
    {
        return $this->estRemunere;
    }

    public function setEstRemunere(bool $estRemunere): static
    {
        $this->estRemunere = $estRemunere;
        return $this;
    }

    public function getMaxHelpers(): ?int
    {
        return $this->maxHelpers;
    }

    public function setMaxHelpers(?int $maxHelpers): static
    {
        $this->maxHelpers = $maxHelpers;

        return $this;
    }
}
