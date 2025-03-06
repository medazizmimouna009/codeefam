<?php
namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]

class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
   
    private ?string $nomCategorie = null;

    #[ORM\Column(type: Types::TEXT)]
    
    private ?string $description = null;

    /**
     * @var Collection<int, Cours>
     */
    #[ORM\OneToMany(targetEntity: Cours::class, mappedBy: 'categorie',cascade: ['remove'])]
    private Collection $cours;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\OneToMany(targetEntity: NomCours::class, mappedBy: 'categorie',cascade: ['remove'])]
    private Collection $nomCours;

    public function __construct()
    {
      //  $this->cours = new ArrayCollection();
        $this->nomCours = new ArrayCollection();
    }



    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): static
    {
        $this->nomCategorie = $nomCategorie;

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
            $cour->setCategorie($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): static
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getCategorie() === $this) {
                $cour->setCategorie(null);
            }
        }

        return $this;
    }


    // Getters et setters pour les noms de cours
    public function getNomCours(): Collection
    {
        return $this->nomCours;
    }

    public function addNomCour(NomCours $nomCour): self
    {
        if (!$this->nomCours->contains($nomCour)) {
            $this->nomCours->add($nomCour);
            $nomCour->setCategorie($this);
        }

        return $this;
    }

    public function removeNomCour(NomCours $nomCour): self
    {
        if ($this->nomCours->removeElement($nomCour)) {
            if ($nomCour->getCategorie() === $this) {
                $nomCour->setCategorie(null);
            }
        }

        return $this;
    }




}
