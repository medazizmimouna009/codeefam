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

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id', nullable: true)]
    private ?User $utilisateur = null; // ID de l'utilisateur

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Le type d'achat ne peut pas être vide.")]
    #[Assert\Choice(choices: ['offre', 'cours'], message: "Le type d'achat doit être soit 'offre' soit 'cours'.")]
    private ?string $typeAchat = null; // 'offre' ou 'cours'

    #[ORM\ManyToOne(targetEntity: Offre::class)]
    #[ORM\JoinColumn(name: 'idOffre', referencedColumnName: 'id', nullable: true)]  // Change 'idOffre' to 'id'
    private ?Offre $offre = null;

  

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: "La date d'achat ne peut pas être vide.")]
    #[Assert\Type(type: \DateTime::class, message: "La date d'achat doit être une date valide.")]
    private ?\DateTimeInterface $dateAchat = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide.")]
    #[Assert\Choice(choices: ['actif', 'expiré', 'annulé'], message: "Le statut doit être l'un des suivants: 'actif', 'expiré', ou 'annulé'.")]
    private ?string $statut = 'actif';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $amount = null;

    #[ORM\Column(nullable: true)]
    private ?int $paymentId = null; // 'actif', 'expiré', ou 'annulé'

    public function __construct()
    {
        $this->dateAchat = new \DateTime(); // Définit la date d'achat automatiquement
    }

    public function getIdAchat(): ?int
    {
        return $this->idAchat;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(User $Utilisateur): static
    {
        $this->utilisateur = $Utilisateur;
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

   
    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }

    public function setPaymentId(?int $paymentId): static
    {
        $this->paymentId = $paymentId;

        return $this;
    }
}
