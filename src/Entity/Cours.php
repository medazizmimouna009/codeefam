<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    
    private ?string $description = null;

    #[ORM\Column]
   
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    private ?string $duree = null;


    #[ORM\Column(length: 255, nullable: true)] 
    private ?string $fichier = null;

    private ?File $fichierFile = null;

    

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;


    
    /**
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'cours')]
    private Collection $videos;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    private ?NomCours $nomCours = null;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->updatedAt = new \DateTime(); // Initialiser avec la date et l'heure actuelles

    }


    #[ORM\ManyToOne(targetEntity: Tuteur::class, inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tuteur $tuteur = null;


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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    /*public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }
*/
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setCours($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getCours() === $this) {
                $video->setCours(null);
            }
        }

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

    public function getNomCours(): ?NomCours
    {
        return $this->nomCours;
    }

    public function setNomCours(?NomCours $nomCours): static
    {
        $this->nomCours = $nomCours;

        return $this;
    }


    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(?Tuteur $tuteur): static
    {
        $this->tuteur = $tuteur;
        return $this;
    }


    public function getFichierFile(): ?File
    {
        return $this->fichierFile;
    }

    public function setFichierFile(?File $fichierFile): static
    {
        $this->fichierFile = $fichierFile;

        // Mettre à jour la date de modification si un fichier est uploadé
        if ($fichierFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getFichier(): ?string
{
    return $this->fichier;
}

    public function setFichier(?string $fichier): static
{
    $this->fichier = $fichier;

    return $this;
}



}
