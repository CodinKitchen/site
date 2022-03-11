<?php

namespace App\Service\Notification;

use App\Notification\TranslatableNotificationInterface;
use Exception;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationFactory
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function createNotification(
        string $type,
        string $subject = '',
        string $content = '',
        array $channels = [],
        array $subjectParameters = [],
        array $contentParameters = [],
    ): Notification {
        if (!is_subclass_of($type, Notification::class)) {
            throw new Exception('Given $type must be instance of ' . Notification::class);
        };

        $notification = new $type($subject, $channels);

        if (is_subclass_of($type, TranslatableNotificationInterface::class)) {
            $notification->setTranslator($this->translator);
            $notification->setSubjectParameters($subjectParameters);
            $notification->setContentParameters($contentParameters);
        };

        return $notification->content($content);
    }
}
