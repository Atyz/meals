<?php

namespace App\Subscriber;

use App\Event\AppEvents;
use App\Event\UserEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordSubscriber implements EventSubscriberInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AppEvents::USER_PASSWORD_RESET => 'hash',
        ];
    }

    public function hash(UserEvent $event)
    {
        $user = $event->getUser();

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        ));

        $user->eraseCredentials();
    }
}
