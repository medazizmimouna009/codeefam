<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $texte = null;
    
    #[ORM\ManyToOne]
    private ?NomCours $nomCours = null;

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'question', cascade: ['persist', 'remove'])]
    private Collection $reponses;

    #[ORM\ManyToMany(targetEntity: Quiz::class, mappedBy: 'questions')]
    private Collection $quizzes;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $niveau = 'facile'; // Valeur par défaut

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
    }

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;
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
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setQuestion($this);
        }
        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->removeElement($reponse)) {
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->addQuestion($this);
        }
        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz)) {
            $quiz->removeQuestion($this);
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->texte ?? 'Nouvelle question';
    }
}