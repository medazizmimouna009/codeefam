<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Webhook;
use App\Entity\User;
use App\Entity\Achat;
use App\Entity\Offre;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;

class StripeService
{
    private string $publicKey;
    private string $secretKey;
    private EntityManagerInterface $entityManager;

    public function __construct(string $publicKey, string $secretKey, EntityManagerInterface $entityManager)
    {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        Stripe::setApiKey($this->secretKey);
        $this->entityManager = $entityManager;
    }

    public function createCheckoutSession(string $name, float $amount, string $currency = 'usd', int $userId, int $offreId): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => $name],
                    'unit_amount' => $amount * 100, 
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://127.0.0.1:8000/payment/success/'.$offreId.'/'.$userId,
            'cancel_url' => 'http://127.0.0.1:8000/payment/cancel',
            'metadata' => [
                'user_id' => $userId,
                'offre_id' => $offreId,
            ],
        ]);
    }

    public function handleWebhook(string $payload, ?string $sigHeader, string $endpointSecret): array
    {
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return ['error' => 'Invalid webhook payload'];
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return ['error' => 'Invalid webhook signature'];
        }
    
        // Handle "checkout.session.completed" event
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
    
            // Ensure metadata exists
            if (!isset($session->metadata->user_id, $session->metadata->offre_id)) {
                return ['error' => 'Missing metadata in session'];
            }
    
            $userId = (int) $session->metadata->user_id;
            $offreId = (int) $session->metadata->offre_id;
            $amountPaid = $session->amount_total / 100;
    
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            $offre = $this->entityManager->getRepository(Offre::class)->find($offreId);
    
            if (!$user || !$offre) {
                return ['error' => 'Invalid user or offer ID'];
            }
    
            // Check if payment already exists to prevent duplicate processing
            $existingPayment = $this->entityManager->getRepository(Achat::class)
                ->findOneBy(['paymentId' => $session->id]);
    
            if ($existingPayment) {
                return ['message' => 'Payment already processed'];
            }
    
            // Save payment record
            $payment = new Achat();
            $payment->setUtilisateur($user);
            $payment->setOffre($offre);
            $payment->setAmount($amountPaid);
            $payment->setStatut('completed');
            $payment->setPaymentId($session->id);
            $payment->setTypeAchat('offre');
            $payment->setDateAchat(new \DateTime());
    
            $this->entityManager->persist($payment);
            $this->entityManager->flush();
    
            return ['message' => 'Payment recorded successfully'];
        }
    
        return ['message' => 'Unhandled event type'];
    }
    
}
