<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class OAuthRegistrationService
{

     /**
     * @param GoogleUser $resourceOwner
     */

    public function persist(ResourceOwnerInterface $resourceOwner, UserRepository $repository): User
    {
        if (!($resourceOwner instanceof GoogleUser)) {
            throw new \RuntimeException('Expected GoogleUser');
        }

        // Get the raw data from Google
        $googleUserData = $resourceOwner->toArray();

        // Extract the profile picture URL
        $profilePictureUrl = $googleUserData['picture'] ?? null;

        var_dump($profilePictureUrl);

        // Modify the URL to request a larger image (optional)
        if ($profilePictureUrl) {
            $profilePictureUrl = str_replace('=s96-c', '=s200-c', $profilePictureUrl);
        } else {
            // Fallback to a default profile picture
            $profilePictureUrl = 'public/uploads/profile_pictures/default-profile.png';
        }

        // Create or update the user
        $user = (new User())
            ->setEmail($resourceOwner->getEmail())
            ->setGoogleId($resourceOwner->getId())
            ->setNom($resourceOwner->getLastName())
            ->setPrenom($resourceOwner->getFirstName())
            ->setPhotoDeProfil($profilePictureUrl)
            ->setIsVerified(true);

        $repository->add($user, true);
        return $user;
    }
}

