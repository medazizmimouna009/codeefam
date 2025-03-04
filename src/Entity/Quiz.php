<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(targetEntity: NomCours::class)]
    private ?NomCours $nomCours = null;

    #[ORM\ManyToMany(targetEntity: Question::class, inversedBy: 'quizzes')]
    private Collection $questions;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $niveau = 'facile'; // Valeur par défaut

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): self
    {
        $this->niveau = $niveau;
        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }





    public function getNomCours(): ?NomCours
    {
        return $this->nomCours;
    }

    public function setNomCours(?NomCours $nomCours): self
    {
        $this->nomCours = $nomCours;
        return $this;
    }



    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->addQuiz($this);
        }
        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            $question->removeQuiz($this);
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? 'Nouveau quiz';
    }
}