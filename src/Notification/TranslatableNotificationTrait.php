<?php

namespace App\Notification;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatableNotificationTrait
{
    private TranslatorInterface $translator;

    private array $subjectParameters = [];

    private array $contentParameters = [];

    private ?string $actionText = null;

    private ?string $actionUrl = null;

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    public function setSubjectParameters(array $subjectParameters): void
    {
        $this->subjectParameters = $subjectParameters;
    }

    public function getSubjectParameters(): array
    {
        return $this->subjectParameters;
    }

    public function setContentParameters(array $contentParameters): void
    {
        $this->contentParameters = $contentParameters;
    }

    public function getContentParameters(): array
    {
        return $this->contentParameters;
    }

    public function setAction(string $actionText, string $actionUrl): void
    {
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
    }

    public function getAction(): ?array
    {
        if ($this->actionText === null || $this->actionUrl === null) {
            return null;
        }

        return ['action_text' => $this->actionText, 'action_url' => $this->actionUrl];
    }
}
