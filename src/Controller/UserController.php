<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
final class UserController extends AbstractController{
    #[Route('/admin/users', name: 'app_user_index', methods: ['GET'])]
    #[Route('/tuteur/users', name: 'app_index_tuteur_side', methods: ['GET'])]
    #[Route('/user/users', name: 'app_index_user_side', methods: ['GET'])]
    public function index(Security $security, Request $request, UserRepository $userRepository): Response
    {
        // Récupération du rôle de l'utilisateur
        if ($security->isGranted('ROLE_ADMIN')) {
            $template = 'user/index.html.twig';  

        } elseif ($security->isGranted('ROLE_TUTEUR')) {
            $template = 'user/indexTuteurSide.html.twig';
        } elseif ($security->isGranted('ROLE_USER')) {
            $template = 'user/indexUserSide.html.twig';
        } else {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }
    
        return $this->render($template, [
            'users' => $userRepository->findAll(),
        ]);
    }
    


 #[IsGranted('ROLE_ADMIN')]   
 #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
 public function new(
     Request $request,
     EntityManagerInterface $entityManager,
     UserPasswordHasherInterface $passwordHasher // Inject the password hasher
 ): Response {
     $user = new User();
     $form = $this->createForm(UserType::class, $user);
     $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {
         // Hash the password before persisting
         $plainPassword = $user->getPassword(); // Assuming the password is set in the form
         $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
         $user->setPassword($hashedPassword);

         $entityManager->persist($user);
         $entityManager->flush();

         return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
     }

     return $this->render('user/new.html.twig', [
         'user' => $user,
         'form' => $form,
     ]);
 }
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_TUTEUR")'), message: 'Unauthorized access!')]

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }


 
    #[IsGranted('ROLE_ADMIN')]      
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]   
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
