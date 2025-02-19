<?php

namespace App\Entity;

use App\Repository\AchatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AchatRepository::class)
 */
#[ORM\Entity(repositoryClass: AchatRepository::class)]
class Achat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idAchat = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'ID de l'utilisateur ne peut pas être vide.")]
    #[Assert\Type(type: 'integer', message: "L'ID de l'utilisateur doit être un entier.")]
    private ?int $idUtilisateur = null; // ID de l'utilisateur

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Le type d'achat ne peut pas être vide.")]
    #[Assert\Choice(choices: ['offre', 'cours'], message: "Le type d'achat doit être soit 'offre' soit 'cours'.")]
    private ?string $typeAchat = null; // 'offre' ou 'cours'

    #[ORM\ManyToOne(targetEntity: Offre::class)]
    #[ORM\JoinColumn(name: 'idOffre', referencedColumnName: 'id', nullable: true)]  // Change 'idOffre' to 'id'
    private ?Offre $offre = null;

    #[ORM\ManyToOne(targetEntity: Cours::class)]
    #[ORM\JoinColumn(name: 'idCours', referencedColumnName: 'id', nullable: true)]  // Change 'idOffre' to 'id'
    private ?Cours $cours = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: "La date d'achat ne peut pas être vide.")]
    #[Assert\Type(type: \DateTime::class, message: "La date d'achat doit être une date valide.")]
    private ?\DateTimeInterface $dateAchat = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Date(message: "La date de fin doit être une date valide.")]
    #[Assert\GreaterThanOrEqual(propertyPath: "dateAchat", message: "La date de fin doit être postérieure ou égale à la date d'achat.")]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide.")]
    #[Assert\Choice(choices: ['actif', 'expiré', 'annulé'], message: "Le statut doit être l'un des suivants: 'actif', 'expiré', ou 'annulé'.")]
    private ?string $statut = 'actif'; // 'actif', 'expiré', ou 'annulé'

    public function __construct()
    {
        $this->dateAchat = new \DateTime(); // Définit la date d'achat automatiquement
    }

    public function getIdAchat(): ?int
    {
        return $this->idAchat;
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(int $idUtilisateur): static
    {
        $this->idUtilisateur = $idUtilisateur;
        return $this;
    }

    public function getTypeAchat(): ?string
    {
        return $this->typeAchat;
    }

    public function setTypeAchat(string $typeAchat): static
    {
        $this->typeAchat = $typeAchat;
        return $this;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): static
    {
        $this->offre = $offre;
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

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }
    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }
    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
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



    public function setDateAchat(?\DateTimeInterface $dateAchat): static
    {
        $this->dateAchat = $dateAchat;
        return $this;
    }
}
