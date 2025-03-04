<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ProfanityFilterService
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

    public function censorText(string $text): string
    {
        $apiUrl = 'https://api.api-ninjas.com/v1/profanityfilter?text=' . urlencode($text);

        try {
            $response = $this->client->request('GET', $apiUrl, [
                'headers' => [
                    'X-Api-Key' => $this->apiKey,
                ]
            ]);

            $content = $response->toArray();

            return $content['censored'] ?? $text; // Return censored text if available, otherwise return original text
        } catch (\Exception $e) {
            $this->logger->error('Profanity Filter API error: ' . $e->getMessage());
            return $text; // Handle errors gracefully
        }
    }

    public function containsProfanity(string $text): bool
    {
        // Implement the logic to check if the text contains profanity
        // This is a placeholder implementation
        $badWords = ['badword1', 'badword2']; // Replace with actual bad words
        foreach ($badWords as $badWord) {
            if (stripos($text, $badWord) !== false) {
                return true;
            }
        }
        return false;
    }
}