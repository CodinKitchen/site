<?php

namespace App\Notification;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class AttendeeNotification extends Notification implements EmailNotificationInterface, TranslatableNotificationInterface
{
    use TranslatableNotificationTrait;

    private const THEME = 'attendee';

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = NotificationEmail::asPublicEmail()
            ->to($recipient->getEmail())
            ->subject($this->getTranslator()->trans($this->getSubject(), $this->getSubjectParameters(), 'email'))
            ->content($this->getTranslator()->trans($this->getContent(), $this->getContentParameters(), 'email'))
            ->theme(self::THEME);
        ;

        if (($action = $this->getAction()) !== null) {
            $email->action($this->getTranslator()->trans($action['action_text'], [], 'email'), $action['action_url']);
        }

        return new EmailMessage($email);
    }
}
