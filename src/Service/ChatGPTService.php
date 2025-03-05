<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ChatGPTService
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
        try {
            $response = $this->client->request('POST', 'https://api.openai.com/v1/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo', // Use latest model
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $userMessage]
                    ],
                    'max_tokens' => 150,
                    'temperature' => 0.7,
                ],
            ]);

            $data = $response->toArray();
            return $data['choices'][0]['message']['content'] ?? 'Sorry, I couldn’t process your request.';

        } catch (\Exception $e) {
            $this->logger->error('ChatGPT API error: ' . $e->getMessage());
            return 'Sorry, an error occurred while processing your request.';
        }
    }
}
