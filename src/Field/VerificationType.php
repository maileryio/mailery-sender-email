<?php

namespace Mailery\Sender\Email\Field;

use Yiisoft\Translator\TranslatorInterface;

class VerificationType
{
    private const DOMAIN = 'domain';
    private const TOKEN = 'token';

    /**
     * @var TranslatorInterface|null
     */
    private ?TranslatorInterface $translator = null;

    /**
     * @param string $value
     */
    public function __construct(
        private string $value
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return static
     */
    public static function typecast(string $value): static
    {
        return new static($value);
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
        $fnTranslate = function (string $message) {
            if ($this->translator !== null) {
                return $this->translator->translate($message);
            }
            return $message;
        };

        return [
            self::DOMAIN => $fnTranslate('Domain verification'),
            self::TOKEN => $fnTranslate('Email confirmation'),
        ][$this->value] ?? 'Unknown';
    }

    /**
     * @return bool
     */
    public function isDomain(): bool
    {
        return $this->getValue() === self::DOMAIN;
    }

    /**
     * @return bool
     */
    public function isToken(): bool
    {
        return $this->getValue() === self::TOKEN;
    }
}
