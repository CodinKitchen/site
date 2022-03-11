<?php

namespace App\Notification;

use Symfony\Contracts\Translation\TranslatorInterface;

interface TranslatableNotificationInterface
{
    public function setTranslator(TranslatorInterface $translator): void;

    public function getTranslator(): TranslatorInterface;

    public function setSubjectParameters(array $subjectParameters): void;

    public function getSubjectParameters(): array;

    public function setContentParameters(array $contentParameters): void;

    public function getContentParameters(): array;

    public function setAction(string $actionText, string $actionUrl): void;
    
    public function getAction(): ?array;
}
