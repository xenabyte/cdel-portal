<?php

namespace App\Libraries\Google;

use Google\Client as GoogleClient;
use Google\Service\Directory;

class Google
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $path = 'public/google/tau-core-1970c12dcf83.json';
        $this->client = new GoogleClient();
        $this->client->setAuthConfig($path);
        $this->client->addScope('https://www.googleapis.com/auth/admin.directory.user');
        $this->service = new Directory($this->client);
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
        } catch (\Google\Service\Exception $e) {
            return false;
        }
    }
}
