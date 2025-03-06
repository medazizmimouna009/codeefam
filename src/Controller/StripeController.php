<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Achat;
use App\Entity\Cours;
use App\Entity\Offre;
use App\Service\StripeService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class StripeController extends AbstractController
{
    #[Route('/user/course/{id}', name: 'paymeny_offre_details')]
    public function index(StripeService $stripeService, ManagerRegistry $doctrine, $id): Response
    {
        $cour = $doctrine->getRepository(Cours::class)->findOneBy(['id' => $id]);

        // Check if the Cours exists
        if (!$cour) {
            throw $this->createNotFoundException('Cours not found');
        }
    
        // Find the related Offre entities by using the owning side
        $offres = $doctrine->getRepository(Offre::class)->findBy(['cour' => $cour]);
        
        // If no Offre found for the given Cours, handle accordingly
        if (!$offres) {
            throw $this->createNotFoundException('Offres not found for the given Cours');
        }
        
        return $this->render('payment/offre/index.html.twig',[
            'offres' => $offres,
        ]);
    }
    
    #[Route('/user/payment/{id}', name: 'app_payment')]
    public function paymentPage($id, ManagerRegistry $doctrine)
    {
        // Check if the user is authenticated
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
    
        $offre = $doctrine->getRepository(Offre::class)->findOneBy(['id' => $id]);
        
        if (!$offre) {
            throw $this->createNotFoundException('Offre not found');
        }
    
        return $this->render('payment/index.html.twig', [
            'offre' => $offre,
        ]);
    }

    
    #[Route('/payment/create-session', name: 'create_payment_session', methods: ['POST'])]
    public function createSessions(Request $request, StripeService $stripeService): Response
    {
        // Get form data
        $userId = $request->request->get('user_id');
        $offreId = $request->request->get('offre_id');
        $offreName = $request->request->get('offre_name');
        $offrePrice = $request->request->get('offre_price');

        // Create Stripe session
        $session = $stripeService->createCheckoutSession($offreName, $offrePrice * 100, 'usd', $userId, $offreId);
        
        // Redirect to Stripe Checkout
        return $this->redirect($session->url);
    }
    
    #[Route('/payment/webhook', name: 'stripe_webhook')]
    public function stripeWebhook(Request $request, StripeService $stripeWebhookService): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $endpointSecret = 'rk_test_51QJOcMLeUzZJDuejZrnzLPSu6IwWM7ZnDxYTTwLiYiTCmW4YgEBtbqieED2mlG2HLXhIJP9toZSjNvsDCE3LkDyC00MTXQtK8Q';
        
        $response = $stripeWebhookService->handleWebhook($payload, $sigHeader, $endpointSecret);

        return new JsonResponse($response, isset($response['error']) ? 400 : 200);
    }

    #[Route('/payment/success/{offre}/{user}', name: 'payment_success')]
    public function success(Request $request, StripeService $stripeService, ManagerRegistry $entityManager,$offre,$user)
    {
        // Retrieve the user and offer from the database
        $userSave = $entityManager->getRepository(User::class)->find($user);
        $offreSave = $entityManager->getRepository(Offre::class)->find($offre);
    
        // Save the payment to the database
        $payment = new Achat();
        $payment->setUtilisateur($userSave);
        $payment->setOffre($offreSave);
        $payment->setAmount($offreSave->getPrix());
        $payment->setStatut('completed');
        $paymentId = random_int(100000, 999999);  // Generate a random integer payment ID
        $payment->setPaymentId($paymentId);
        
        $payment->setTypeAchat('offre');
        $payment->setDateAchat(new \DateTime());
    
        $entityManager->getManager()->persist($payment);
        $entityManager->getManager()->flush();
    
        // Return success response
        return $this->render('payment/payment_success.html.twig', [
        ]);
    }    

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel()
    {
        return $this->render('payment/payment_cancel.html.twig');
    }
}