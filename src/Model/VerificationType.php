<?php

namespace Mailery\Sender\Email\Model;

use InvalidArgumentException;
use Mailery\Sender\Email\Entity\EmailSender;
use Yiisoft\Translator\TranslatorInterface;

class VerificationType
{
    public const DOMAIN = 'domain';
    public const TOKEN = 'token';

    /**
     * @var string
     */
    private string $value;

    /**
     * @var TranslatorInterface|null
     */
    private ?TranslatorInterface $translator = null;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!isset($this->getLabels()[$value])) {
            throw new InvalidArgumentException();
        }
        $this->value = $value;
    }

    /**
     * @param TranslatorInterface $translator
     * @return self
     */
    public function withTranslator(TranslatorInterface $translator): self
    {
        $new = clone $this;
        $new->translator = $translator;

        return $new;
    }

    /**
     * @return self
     */
    public static function asDomain(): self
    {
        return new self(self::DOMAIN);
    }

    /**
     * @return self
     */
    public static function asToken(): self
    {
        return new self(self::TOKEN);
    }

    /**
     * @param EmailSender $entity
     * @return self
     */
    public static function fromEntity(EmailSender $entity): self
    {
        return new self($entity->getVerificationType());
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getLabels()[$this->value] ?? '';
    }

    /**
     * @return array
     */
    public function getLabels() : array
    {
        return [
            self::DOMAIN => $this->translate('Domain verification'),
            self::TOKEN => $this->translate('Email confirmation'),
        ];
    }

    /**
     * @param string $message
     * @return string
     */
    private function translate(string $message): string
    {
        if ($this->translator !== null) {
            return $this->translator->translate($message);
        }
        return $message;
    }
}
