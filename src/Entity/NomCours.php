<?php

namespace App\Entity;

use App\Repository\NomCoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;




#[ORM\Entity(repositoryClass: NomCoursRepository::class)]
#[UniqueEntity(fields: ['nom'], message: 'Ce nom de cours existe déjà.')]

class NomCours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Cours>
     */
    #[ORM\OneToMany(targetEntity: Cours::class, mappedBy: 'nomCours')]
    private Collection $cours;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->seancesEnLigne = new ArrayCollection();

    }

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'nomCours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

   /* #[ORM\OneToMany(targetEntity: SeanceEnLigne::class, mappedBy: 'nomCours', cascade: ['persist', 'remove'])]
    private Collection $seancesEnLigne;*/


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

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): static
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setNomCours($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): static
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getNomCours() === $this) {
                $cour->setNomCours(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }


    /*public function getSeancesEnLigne(): Collection
    {
        return $this->seancesEnLigne;
    }

    public function addSeanceEnLigne(SeanceEnLigne $seanceEnLigne): self
    {
        if (!$this->seancesEnLigne->contains($seanceEnLigne)) {
            $this->seancesEnLigne->add($seanceEnLigne);
            $seanceEnLigne->setNomCours($this);
        }
        return $this;
    }
    public function removeSeanceEnLigne(SeanceEnLigne $seanceEnLigne): self
    {
        if ($this->seancesEnLigne->removeElement($seanceEnLigne)) {
            if ($seanceEnLigne->getNomCours() === $this) {
                $seanceEnLigne->setNomCours(null);
            }
        }
        return $this;
    }
*/


public function __toString(): string
    {
        return $this->nom;

}

}
