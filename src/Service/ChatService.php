<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ChatService
{
    private HttpClientInterface $client;
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $client, string $apiKey, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    public function getChatResponse(string $userMessage): string
    {
        $apiUrl = 'https://api.chatbot.com/v1/conversations';

        try {
            $response = $this->client->request('POST', $apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messages' => [
                        ['role' => 'user', 'content' => $userMessage]
                    ]
                ],
            ]);

            $content = $response->toArray();

            return $content['messages'][0]['content'] ?? 'Sorry, I couldn’t process your request.';
        } catch (\Exception $e) {
            $this->logger->error('Chat API error: ' . $e->getMessage());
            return 'Sorry, an error occurred while processing your request.';
        }
    }
}
