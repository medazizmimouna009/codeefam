<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: VideoRepository::class)]

class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoFile = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAjout = null;

    public function __construct()
    {
        $this->dateAjout = new \DateTime(); // Initialise la date actuelle
        $this->ratings = new ArrayCollection();
    }

    #[ORM\ManyToOne(inversedBy: 'videos')]
    private ?Cours $cours = null;

    /**
     * @var Collection<int, Rating>
     */
    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'video')]
    private Collection $ratings;

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
    public function setVideoFile(?File $videoFile = null): void
    {
        $this->videoFile = $videoFile;

        // Mettre à jour la date de modification si un fichier est uploadé
        if ($videoFile) {
            $this->dateAjout = new \DateTime();
        }
    }

    public function getVideoFile(): ?File
    {
        return $this->videoFile;
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


    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): static
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): static
    {
        $this->cours = $cours;

        return $this;
    }
    public function getVideoName(): ?string
    {
        return $this->videoName;
    }

    public function setVideoName(?string $videoName): void
    {
        $this->videoName = $videoName;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setVideo($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getVideo() === $this) {
                $rating->setVideo(null);
            }
        }

        return $this;
    }
    // src/Entity/Video.php
public function getAverageRating(): float
{
    $ratings = $this->getRatings();

    if ($ratings->isEmpty()) {
        return 0; // Aucune note
    }

    $total = 0;
    foreach ($ratings as $rating) {
        $total += $rating->getValue(); // Assurez-vous que getValue() retourne un nombre
    }

    // Calcule la moyenne avec une précision de 2 décimales
    return round($total / $ratings->count(), 2);
}
public function getUserRating(User $user): ?Rating
{
    // Parcourir toutes les notes de la vidéo
    foreach ($this->ratings as $rating) {
        // Si l'utilisateur correspond, retourner la note
        if ($rating->getUser() === $user) {
            return $rating;
        }
    }

    // Retourner null si l'utilisateur n'a pas encore noté la vidéo
    return null;
}
}

