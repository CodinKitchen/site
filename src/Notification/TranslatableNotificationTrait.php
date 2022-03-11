<?php

namespace App\Notification;

use PhpParser\Node\Expr\Cast\Int_;
use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatableNotificationTrait
{
    private TranslatorInterface $translator;

    /**
     * @var array<string, string|int|null> $subjectParameters
     */
    private array $subjectParameters = [];

    /**
     * @var array<string, string|int|null> $contentParameters
     */
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

    /**
     * @param array<string, string|int|null> $subjectParameters
     */
    public function setSubjectParameters(array $subjectParameters): void
    {
        $this->subjectParameters = $subjectParameters;
    }

    /**
     * @return array<string, string|int|null>
     */
    public function getSubjectParameters(): array
    {
        return $this->subjectParameters;
    }

    /**
     * @param array<string, string|int|null> $contentParameters
     */
    public function setContentParameters(array $contentParameters): void
    {
        $this->contentParameters = $contentParameters;
    }

    /**
     * @return array<string, string|int|null>
     */
    public function getContentParameters(): array
    {
        return $this->contentParameters;
    }

    public function setAction(string $actionText, string $actionUrl): void
    {
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
    }

    /**
     * @return array<string, string>|null
     */
    public function getAction(): ?array
    {
        if ($this->actionText === null || $this->actionUrl === null) {
            return null;
        }

        return ['action_text' => $this->actionText, 'action_url' => $this->actionUrl];
    }
}
