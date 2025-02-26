<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class GeminiService
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

    public function getGeminiResponse(string $userMessage): string
    {
        $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro-001:generateContent?key=' . $this->apiKey;
        $waitTimes = [1, 2, 4, 8]; // Seconds

        foreach ($waitTimes as $wait) {
            try {
                $this->logger->info('Sending request to Gemini API', ['url' => $url, 'message' => $userMessage]);

                $response = $this->client->request('POST', $url, [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json' => [
                        'contents' => [
                            ['parts' => [['text' => $userMessage]]]
                        ]
                    ],
                    'timeout' => 60, // Increase the timeout duration to 60 seconds
                ]);

                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorBody = $response->getContent(false);
                    $this->logger->error('Gemini API Error', ['response' => $errorBody]);

                    // Check if the error is a 503 Service Unavailable
                    if ($statusCode === 503) {
                        sleep($wait);
                        continue;
                    }

                    // Check if the error is a 429 Resource Exhausted
                    if ($statusCode === 429) {
                        return 'The API quota has been exhausted. Please try again later.';
                    }

                    return 'Gemini API Error: ' . $errorBody;
                }

                $data = $response->toArray();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I couldn’t process your request.';

            } catch (\Exception $e) {
                $this->logger->error('Error calling Gemini API', ['error' => $e->getMessage()]);

                // Check if the error is a 503 Service Unavailable
                if (strpos($e->getMessage(), '503') !== false) {
                    sleep($wait);
                    continue;
                }

                // Check if the error is a 429 Resource Exhausted
                if (strpos($e->getMessage(), '429') !== false) {
                    return 'The API quota has been exhausted. Please try again later.';
                }

                return 'An error occurred: ' . $e->getMessage();
            }
        }

        return 'An error occurred while processing your request.';
    }
}

