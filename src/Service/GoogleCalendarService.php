<?php

namespace App\Service;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Symfony\Component\HttpFoundation\RequestStack;

class GoogleCalendarService
{
    private $client;
    private $requestStack;

    public function __construct(RequestStack $requestStack)
{
    $this->requestStack = $requestStack;
    $this->client = new Google_Client();
    $this->client->setClientId('709694524164-0bedioiq18hclo4i80c4bbsgeq64o66u.apps.googleusercontent.com');
    $this->client->setClientSecret('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');
    $this->client->setRedirectUri('http://localhost:8000/evenement/google-callback');
    $this->client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);
    $this->client->setAccessType('offline');
}
public function getAuthUrl(): string
{
    return $this->client->createAuthUrl();
}

public function authenticate(string $code): void
{
    $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
    if (isset($accessToken['error'])) {
        throw new \Exception('Erreur d’authentification : ' . $accessToken['error_description']);
    }
    $this->client->setAccessToken($accessToken);
    $session = $this->requestStack->getSession();
    $session->set('google_access_token', $accessToken);
}

public function createEvent(array $eventData): string
{
    $session = $this->requestStack->getSession();
    $accessToken = $session->get('google_access_token');

    if (!$accessToken) {
        throw new \Exception('No access token available. Please authenticate first.');
    }

    $this->client->setAccessToken($accessToken);

    if ($this->client->isAccessTokenExpired()) {
        $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
        $session->set('google_access_token', $this->client->getAccessToken());
    }

    $service = new Google_Service_Calendar($this->client);

    $event = new Google_Service_Calendar_Event([
        'summary' => $eventData['title'],
        'location' => $eventData['location'],
        'description' => $eventData['description'],
        'start' => [
            'dateTime' => $eventData['start']->format('c'),
            'timeZone' => 'UTC',
        ],
        'end' => [
            'dateTime' => $eventData['end'] ? $eventData['end']->format('c') : $eventData['start']->modify('+1 hour')->format('c'),
            'timeZone' => 'UTC',
        ],
    ]);

    $calendarId = 'primary';
    $event = $service->events->insert($calendarId, $event);
    return $event->htmlLink;
}
}