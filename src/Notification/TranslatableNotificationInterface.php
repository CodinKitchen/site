<?php

namespace App\Notification;

use Symfony\Contracts\Translation\TranslatorInterface;

interface TranslatableNotificationInterface
{
    public function setTranslator(TranslatorInterface $translator): void;

    public function getTranslator(): TranslatorInterface;

    /**
     * @param array<string, string|int|null> $subjectParameters
     */
    public function setSubjectParameters(array $subjectParameters): void;

    /**
     * @return array<string, string|int|null>
     */
    public function getSubjectParameters(): array;

    /**
     * @param array<string, string|int|null> $contentParameters
     */
    public function setContentParameters(array $contentParameters): void;

    /**
     * @return array<string, string|int|null>
     */
    public function getContentParameters(): array;

    public function setAction(string $actionText, string $actionUrl): void;

    /**
     * @return array<string, string>|null
     */
    public function getAction(): ?array;
}
