<?php

namespace App\Notification;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class AdminNotification extends Notification implements EmailNotificationInterface, TranslatableNotificationInterface
{
    use TranslatableNotificationTrait;

    private const THEME = 'admin';

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = (new NotificationEmail())
            ->to($recipient->getEmail())
            ->subject($this->getTranslator()->trans($this->getSubject(), $this->getSubjectParameters(), 'email'))
            ->content($this->getTranslator()->trans($this->getContent(), $this->getContentParameters(), 'email'))
            ->theme(self::THEME)
        ;

        return new EmailMessage($email);
    }
}
