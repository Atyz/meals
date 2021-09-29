<?php

namespace App\Event;

use App\Entity\User;
use App\Entity\UserForgotPassword;
use App\Entity\UserPassword;
use Symfony\Contracts\EventDispatcher\Event;

class UserPasswordRequestEvent extends Event implements EmailEventInterface
{
    protected UserPassword $passwordRequest;

    public function getUser(): User
    {
        return $this->passwordRequest->getUser();
    }

    public function __construct(UserPassword $passwordRequest)
    {
        $this->passwordRequest = $passwordRequest;
    }

    public function getEmailTemplate(): string
    {
        return 'mails/password-request.html.twig';
    }

    public function getEmailOptions(): array
    {
        return [
            'passwordRequest' => $this->passwordRequest,
        ];
    }
}
