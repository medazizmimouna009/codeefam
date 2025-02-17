<?php
// src/Entity/Admin.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Admin extends User
{
    public function __construct()
    {
        parent::__construct();
        $this->setRole('ROLE_ADMIN'); // Définit le rôle par défaut pour un admin
    }
    
}