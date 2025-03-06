<?php 
// src/Entity/Tuteur.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Tuteur extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Specialite cannot be blank.')]
    private ?string $specialite = null;

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): static
    {
        $this->specialite = $specialite;
        return $this;
    }


    public function __construct()
    {
        parent::__construct();
        $this->setRole('ROLE_TUTEUR'); // Définit le rôle par défaut pour un admin
    } 
}