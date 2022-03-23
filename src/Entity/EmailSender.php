<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Entity;

use Mailery\Sender\Entity\Sender;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Email\Entity\Embedded\Verification;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Relation\Embedded;
use Cycle\Annotated\Annotation\Inheritance\SingleTable;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;

#[Entity]
#[SingleTable(value: EmailSender::class)]
class EmailSender extends Sender implements RoutableEntityInterface, LoggableEntityInterface
{
    use LoggableEntityTrait;

    #[Column(type: 'string(255)', nullable: true)]
    private ?string $email = null;

    #[Column(type: 'string(255)', nullable: true)]
    private ?string $replyName = null;

    #[Column(type: 'string(255)', nullable: true)]
    private ?string $replyEmail = null;

    #[Embedded(target: Verification::class, prefix: 'verification_')]
    public Verification $verification;

    #[BelongsTo(target: Domain::class, nullable: true)]
    private ?Domain $domain = null;

    public function __construct()
    {
        $this->verification = new Verification();
    }

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
     * @return Verification
     */
    public function getVerification(): Verification
    {
        return $this->verification;
    }

    /**
     * @param Verification $verification
     * @return self
     */
    public function setVerification(Verification $verification): self
    {
        $this->verification = $verification;

        return $this;
    }

    /**
     * @return Domain|null
     */
    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     * @return self
     */
    public function setDomain(Domain $domain): self
    {
        $this->domain = $domain;

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
        return '/sender/email/delete';
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

}
