<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\ValueObject;

use Mailery\Sender\Email\Form\SenderForm;
use Mailery\Channel\Entity\Channel;

class SenderValueObject
{
    /**
     * @var Channel
     */
    private Channel $channel;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $replyName;

    /**
     * @var string
     */
    private string $replyEmail;


    /**
     * @var string|null
     */
    private ?string $description = null;

    /**
     * @param SenderForm $form
     * @return self
     */
    public static function fromForm(SenderForm $form): self
    {
        $new = new self();
        $new->channel = $form->getChannel();
        $new->name = $form->getName();
        $new->email = $form->getEmail();
        $new->replyName = $form->getReplyName();
        $new->replyEmail = $form->getReplyEmail();
        $new->description = $form->getDescription();

        return $new;
    }

    /**
     * @return Channel
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getReplyName(): string
    {
        return $this->replyName;
    }

    /**
     * @return string
     */
    public function getReplyEmail(): string
    {
        return $this->replyEmail;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
