<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use App\Entity\User;
use App\Entity\Achat;
use App\Entity\Offre;
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

    /**
     * Create a Stripe Checkout Session.
     *
     * @param string $name The name of the product/offer.
     * @param float $amount The amount to charge (in dollars).
     * @param string $currency The currency code (default: 'usd').
     * @param int $userId The ID of the user making the payment.
     * @param int $offreId The ID of the offer being purchased.
     * @return Session The Stripe Checkout Session.
     */
    public function createCheckoutSession(string $name, float $amount, string $currency = 'usd', int $userId, int $offreId): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => $name],
                    'unit_amount' => $amount * 100, // Convert to cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://127.0.0.1:8000/payment/success/' . $offreId . '/' . $userId,
            'cancel_url' => 'http://127.0.0.1:8000/payment/cancel',
            'metadata' => [
                'user_id' => $userId,
                'offre_id' => $offreId,
            ],
        ]);
    }

    /**
     * Handle Stripe webhook events.
     *
     * @param string $payload The raw webhook payload.
     * @param string|null $sigHeader The Stripe signature header.
     * @param string $endpointSecret The webhook endpoint secret.
     * @return array Response indicating success or failure.
     */
    public function handleWebhook(string $payload, ?string $sigHeader, string $endpointSecret): array
    {
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return ['error' => 'Invalid webhook payload'];
        } catch (SignatureVerificationException $e) {
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
            $amountPaid = $session->amount_total / 100; // Convert back to dollars

            // Fetch user and offer entities
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