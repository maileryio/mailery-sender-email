<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Entity;

use Mailery\Sender\Entity\Sender;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mailery\Sender\Domain\Entity\Domain;

/**
 * @Cycle\Annotated\Annotation\Entity
 */
class EmailSender extends Sender implements RoutableEntityInterface, LoggableEntityInterface
{
    use LoggableEntityTrait;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(255)", nullable = true)
     * @var string
     */
    private $email;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(255)")
     * @var string
     */
    private $replyName;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(255)")
     * @var string
     */
    private $replyEmail;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "enum(domain, token)", nullable = true)
     * @var string
     */
    private $verificationType;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(255)", nullable = true)
     * @var string
     */
    private $verificationToken;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getReplyName(): string
    {
        return $this->replyName;
    }

    /**
     * @param string $replyName
     * @return self
     */
    public function setReplyName(string $replyName): self
    {
        $this->replyName = $replyName;

        return $this;
    }

    /**
     * @return string
     */
    public function getReplyEmail(): string
    {
        return $this->replyEmail;
    }

    /**
     * @param string $replyEmail
     * @return self
     */
    public function setReplyEmail(string $replyEmail): self
    {
        $this->replyEmail = $replyEmail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVerificationType(): ?string
    {
        return $this->verificationType;
    }

    /**
     * @param string $verificationType
     * @return self
     */
    public function setVerificationType(string $verificationType): self
    {
        $this->verificationType = $verificationType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    /**
     * @param string $verificationToken
     * @return self
     */
    public function setVerificationToken(string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIndexRouteName(): ?string
    {
        return '/sender/email/index';
    }

    /**
     * @inheritdoc
     */
    public function getIndexRouteParams(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteName(): ?string
    {
        return '/sender/email/view';
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteName(): ?string
    {
        return '/sender/email/edit';
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getDeleteRouteName(): ?string
    {
        return '/user/default/delete';
    }

    /**
     * @inheritdoc
     */
    public function getDeleteRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function isSameDomain(string $domain): bool
    {
        $emailDomain = explode('@', $this->getEmail())[1];
        return $emailDomain === $domain;
    }

    /**
     * @param Domain $domain
     * @return self
     */
    public function verifyDomain(Domain $domain): self
    {
        if ($this->isSameDomain($domain->getDomain())) {
            $this->setVerificationType(self::VERIFICATION_DOMAIN);

            if ($domain->isVerified()) {
                $this->setStatus(self::STATUS_ACTIVE);
            }
        }

        return $this;
    }

    /**
     * @param string $verificationToken
     * @return self
     */
    public function verifyVerificationToken(string $verificationToken): self
    {
        if ($verificationToken === $this->getVerificationToken()) {
            $this->setStatus(self::STATUS_ACTIVE);
            $this->setVerificationType(self::VERIFICATION_TOKEN);
        }

        return $this;
    }
}
