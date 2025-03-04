<?php

// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Tuteur;
use App\Entity\Admin;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator', type: 'string')]
#[ORM\DiscriminatorMap([
    'user' => User::class,
    'tuteur' => Tuteur::class,
    'admin' => Admin::class,
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $role = 'ROLE_USER'; 

    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private bool $isVerified = false;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(type: 'date',nullable: true)]
    private ?\DateTimeInterface $dateDeNaissance = null;

    #[ORM\Column(length: 20,nullable: true)]
    private ?string $numTel = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoDeProfil = null;

    #[ORM\Column(type: 'datetime',nullable: true)]
    private ?\DateTimeInterface $dateInscrit = null;

    #[ORM\Column(length: 255, nullable: true,unique: true)]
    private ?string $google_id = null;

    public function __construct()
    {
        $this->dateInscrit = new \DateTime(); // Set the registration date automatically
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return [$this->role]; // Return role as an array (required by Symfony)
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }
     public function hashPassword(UserPasswordHasherInterface $passwordHasher): void
    {
        if ($this->password) {
            $this->password = $passwordHasher->hashPassword($this, $this->password);
        }
    }
    public function eraseCredentials(): void
    {
        // Clear temporary sensitive data if needed
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
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

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->dateDeNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $dateDeNaissance): static
    {
        $this->dateDeNaissance = $dateDeNaissance;
        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->numTel;
    }

    public function setNumTel(string $numTel): static
    {
        $this->numTel = $numTel;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getPhotoDeProfil(): ?string
    {
        return $this->photoDeProfil;
    }

    public function setPhotoDeProfil(?string $photoDeProfil): static
    {
        $this->photoDeProfil = $photoDeProfil;
        return $this;
    }

    public function getDateInscrit(): ?\DateTimeInterface
    {
        return $this->dateInscrit;
    }

    public function setDateInscrit(\DateTimeInterface $dateInscrit): static
    {
        $this->dateInscrit = $dateInscrit;
        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->google_id;
    }

    public function setGoogleId(?string $google_id): static
    {
        $this->google_id = $google_id;

        return $this;
    }
}