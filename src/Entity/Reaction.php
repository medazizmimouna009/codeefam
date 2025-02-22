<?php

// src/Entity/Reaction.php
namespace App\Entity;

use App\Repository\ReactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReactionRepository::class)]
class Reaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[ORM\ManyToOne(targetEntity: Commentaire::class, inversedBy: 'reactions')]
    private ?Commentaire $commentaire = null;

    #[ORM\Column(length: 10)]
    private ?string $emoji = null;

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getPost(): ?Post { return $this->post; }
    public function setPost(?Post $post): static { $this->post = $post; return $this; }

    public function getCommentaire(): ?Commentaire { return $this->commentaire; }
    public function setCommentaire(?Commentaire $commentaire): static { $this->commentaire = $commentaire; return $this; }

    public function getEmoji(): ?string { return $this->emoji; }
    public function setEmoji(string $emoji): static { $this->emoji = $emoji; return $this; }
}
