<?php

namespace App\Libraries\Google;

use Google\Client as GoogleClient;
use Google\Service\Directory;
use Google\Service\Calendar;
use Log;
use Config;

class Google
{
    protected $client;
    protected $directoryService;
    protected $calendarService;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->setAuthConfig(base_path('public/google/tau-core-api-2551c52d28f8.json'));
        $this->client->addScope([
            'https://www.googleapis.com/auth/admin.directory.user',
            'https://www.googleapis.com/auth/admin.directory.group',
            'https://www.googleapis.com/auth/admin.directory.group.member',
            Calendar::CALENDAR_EVENTS
        ]);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        $this->client->setIncludeGrantedScopes(true);
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback($code)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->client->setAccessToken($accessToken);

        // Save the access token for future use
        session(['google_access_token' => $accessToken]);
    }

    protected function getClient()
    {
        if (session()->has('google_access_token')) {
            $this->client->setAccessToken(session('google_access_token'));

            // Refresh the token if it's expired
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                session(['google_access_token' => $this->client->getAccessToken()]);
            }
        } else {
            throw new \Exception('User not authenticated with Google.');
        }

        return $this->client;
    }

    public function createUser($email, $firstName, $lastName, $password)
    {
        $client = $this->getClient();
        $this->directoryService = new Directory($client);

        $user = new Directory\User();
        $user->setPrimaryEmail($email);
        $user->setName(new Directory\UserName());
        $user->getName()->setGivenName($firstName);
        $user->getName()->setFamilyName($lastName);
        $user->setPassword($password);
        $user->setChangePasswordAtNextLogin(true);

        try {
            $result = $this->directoryService->users->insert($user);
            return $result;
        } catch (\Google\Service\Exception $e) {
            Log::info("Message: " . $e->getMessage());
            return false;
        }
    }

    public function generateGoogleMeetLink($title, $date, $time)
    {
        $client = $this->getClient();
        $this->calendarService = new Calendar($client);

        $requestId = uniqid();

        $event = new Calendar\Event([
            'summary' => $title,
            'start' => [
                'dateTime' => $date . 'T' . $time . ':00',
                'timeZone' => Config::get('app.timezone'),
            ],
            'end' => [
                'dateTime' => $date . 'T' . $time . ':00',
                'timeZone' => Config::get('app.timezone'),
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => $requestId,
                ],
            ],
        ]);

        $calendarId = 'primary'; // Or your specific calendar ID
        $event = $this->calendarService->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);

        return $event->getHangoutLink();
    }

    public function addMemberToGroup($email, $groupEmail)
    {
        $client = $this->getClient();
        $this->directoryService = new Directory($client);

        $member = new Directory\Member();
        $member->setEmail($email);
        $member->setRole('MEMBER');

        try {
            $this->directoryService->members->insert($groupEmail, $member);
            return true;
        } catch (\Google\Service\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
}
