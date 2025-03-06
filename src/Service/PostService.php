<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostService
{
    private EntityManagerInterface $entityManager;
    private string $imagesDirectory;

    public function __construct(EntityManagerInterface $entityManager, string $imagesDirectory)
    {
        $this->entityManager = $entityManager;
        $this->imagesDirectory = $imagesDirectory;
    }

    public function createPost(string $content, ?UploadedFile $imageFile, ?User $user): Post
    {
        $post = new Post();
        $post->setContenu($content);
        $post->setDateCreation(new \DateTime());

        if ($imageFile) {
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            $imageFile->move($this->imagesDirectory, $newFilename);
            $post->setImage($newFilename);
        }

        if ($user) {
            $post->setIdUser($user);
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }
}
