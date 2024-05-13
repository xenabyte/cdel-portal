<?php

namespace App\Libraries\Google;

use Google\Client as GoogleClient;
use Google\Service\Directory;
use Log;

use Google\Service\Calendar;
use Config;

class Google
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $path = base_path('public/google/tau-core-api-2551c52d28f8.json');
        $this->client = new GoogleClient();
        $this->client->setAuthConfig($path);
        $this->client->setSubject(env('GOOGLE_CLIENT_SUBJECT'));
        $this->client->addScope('https://www.googleapis.com/auth/admin.directory.user');
        $this->client->setApplicationName('Your Application Name');

        $this->service = new Directory($this->client);
        $this->client->setScopes([
            'https://www.googleapis.com/auth/admin.directory.group',
            'https://www.googleapis.com/auth/admin.directory.group.member'
        ]);
    }

    public function createUser($email, $firstName, $lastName, $password)
    {
        $user = new Directory\User();
        $user->setPrimaryEmail($email);
        $user->setName(new Directory\UserName());
        $user->getName()->setGivenName($firstName);
        $user->getName()->setFamilyName($lastName);
        $user->setPassword($password);
        $user->setChangePasswordAtNextLogin(true);

        try {
            $result = $this->service->users->insert($user);
            return $result;
        }catch (\Google\Service\Exception $e) {
            // Log or print the error message for debugging
            Log::info("Message: ". $e->getMessage());
            return false;
        }
    }

    public function generateGoogleMeetLink($title, $date, $time)
    {
        $path = base_path('public/google/tau-core-api-d3dd9b502e4d');

        $client = new GoogleClient();
        $client->setScopes(Calendar::CALENDAR_EVENTS);
        $client->setAuthConfig($path);
        $service = new Calendar($client);

        $requestId = uniqid(); 

        $event = new CalendarEvent(array(
            'summary' => $title,
            'start' => array(
                'dateTime' => $date . 'T' . $time . ':00',
                'timeZone' => Config::get('app.timezone'),
            ),
            'end' => array(
                'dateTime' => $date . 'T' . $time . ':00',
                'timeZone' => Config::get('app.timezone'),
            ),
            'conferenceData' => array(
                'createRequest' => array(
                    'requestId' => $requestId,
                ),
            ),
        ));

        $calendarId = 'primary'; // Or your specific calendar ID
        $event = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);

        return $event->getHangoutLink();
    }

    public function addMemberToGroup($email, $groupEmail)
    {
        $service = new Google_Service_Directory($this->client);

        $member = new Google_Service_Directory_Member();
        $member->setEmail($email);
        $member->setRole('MEMBER');

        try {
            $service->members->insert($groupEmail, $member);
            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage);
            return false;
        }
    }
}
