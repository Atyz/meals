<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment as Twig;

class MailerService
{
    const FROM = 'ne-pas-repondre@meals.fr';

    protected MailerInterface $mailer;
    protected Twig $twig;

    public function __construct(MailerInterface $mailer, Twig $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(
        string $templateName,
        array $templateDatas,
        string $recipient,
        array $attachments = []
    ) {
        $template = $this->twig->load($templateName);

        $message = (new Email())
            ->from(self::FROM)
            ->to($recipient)
            ->subject($template->renderBlock('subject', $templateDatas))
            ->html($template->renderBlock('html', $templateDatas))
        ;

        foreach ($attachments as $path) {
            $message->attachFromPath($path);
        }

        $this->mailer->send($message);

        return $message;
    }
}
