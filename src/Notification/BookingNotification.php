<?php

namespace App\Notification;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BookingNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(public TranslatorInterface $translator, string $subject, array $channels = [])
    {
        parent::__construct($subject, $channels);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = NotificationEmail::asPublicEmail()
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->content()
            ->action('Sign in', $this->loginLinkDetails->getUrl())
        ;

        return new EmailMessage($email);
    }
}
