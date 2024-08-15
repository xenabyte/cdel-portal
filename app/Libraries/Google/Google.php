<?php

namespace App\Libraries\Google;

use Google\Client as GoogleClient;
use Google\Service\Directory;
use Log;

class Google
{
    protected $client;
    protected $directoryService;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setAuthConfig(base_path('public/google/tau-core-api-d3dd9b502e4d.json'));
        $this->client->addScope([
            Directory::ADMIN_DIRECTORY_USER,
            Directory::ADMIN_DIRECTORY_GROUP
        ]);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        $this->client->setSubject(env('GOOGLE_CLIENT_SUBJECT'));
    }

    public function authenticate()
    {
        if (!session()->has('google_access_token')) {
            $authUrl = $this->client->createAuthUrl();
            return redirect($authUrl);
        }

        $this->client->setAccessToken(session('google_access_token'));

        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            session(['google_access_token' => $this->client->getAccessToken()]);
        }
    }

    public function createUser($email, $firstName, $lastName, $password, $groupEmail=null)
    {
        try {
            $client = $this->getClient();
            $this->directoryService = new Directory($client);


            $user = new Directory\User([
                'primaryEmail' => $email,
                'name' => [
                    'givenName' => $firstName,
                    'familyName' => $lastName,
                ],
                'password' => $password,
                'changePasswordAtNextLogin' => true
            ]);

            $user = $this->directoryService->users->insert($user);
            if($user){
                $this->addMemberToGroup($email, $groupEmail);
            }
            return $user;
        } catch (\Google\Service\Exception $e) {
            Log::error('Google Service Error: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
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
            $existingMember = $this->directoryService->members->get($groupEmail, $email);
    
            if ($existingMember) {
                return true;
            }
        } catch (\Google\Service\Exception $e) {
            if ($e->getCode() == 404) {
                try {
                    $this->directoryService->members->insert($groupEmail, $member);
                    return true;
                } catch (\Google\Service\Exception $insertException) {
                    Log::error('Failed to add member to group: ' . $insertException->getMessage());
                    return false;
                }
            } else {
                Log::error('Error checking if member exists: ' . $e->getMessage());
                return false;
            }
        }
    
        return false;
    }
    

    protected function getClient()
    {
        if (session()->has('google_access_token')) {
            $this->client->setAccessToken(session('google_access_token'));

            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken(session('google_access_token'));
                session(['google_access_token' => session('google_access_token')]);
            }
        } else {
            $accessToken = $this->client->fetchAccessTokenWithAssertion();
            session(['google_access_token' =>  $accessToken['access_token']]);
            $this->client->setAccessToken(session('google_access_token'));
        }

        return $this->client;
    }
}
