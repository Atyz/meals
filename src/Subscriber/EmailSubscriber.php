<?php

namespace App\Subscriber;

use App\Event\AppEvents;
use App\Event\EmailEventInterface;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailSubscriber implements EventSubscriberInterface
{
    private MailerService $mailer;

    public function __construct(MailerService $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AppEvents::USER_PASSWORD_REQUEST => 'sendToUser',
        ];
    }

    public function sendToUser(EmailEventInterface $event)
    {
        $datas = ['user' => $event->getUser()];
        $datas = array_merge($datas, $event->getEmailOptions());

        $this->mailer->send(
            $event->getEmailTemplate(),
            $datas,
            $event->getUser()->getEmail()
        );
    }
}
