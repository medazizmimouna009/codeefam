<?php

namespace App\Tests\Service;

use App\Service\ProfanityFilterService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Psr\Log\NullLogger;

class ProfanityFilterServiceTest extends TestCase
{
    public function testCensorText()
    {
        $mockResponse = new MockResponse(json_encode(['censored_text' => '****']));
        $client = new MockHttpClient($mockResponse);
        $logger = new NullLogger();
        $service = new ProfanityFilterService($client, 'test_api_key', $logger);

        $result = $service->censorText('badword');
        $this->assertEquals('****', $result);
    }

    public function testCensorTextWithError()
    {
        $mockResponse = new MockResponse('Internal Server Error', ['http_code' => 500]);
        $client = new MockHttpClient($mockResponse);
        $logger = new NullLogger();
        $service = new ProfanityFilterService($client, 'test_api_key', $logger);

        $result = $service->censorText('badword');
        $this->assertEquals('badword', $result);
    }
}
