<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\ValueObject;

use Mailery\Sender\Email\Form\SenderForm;

class SenderValueObject
{
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
     * @param SenderForm $form
     * @return self
     */
    public static function fromForm(SenderForm $form): self
    {
        $new = new self();
        $new->name = $form->getName();
        $new->email = $form->getEmail();
        $new->replyName = $form->getReplyName();
        $new->replyEmail = $form->getReplyEmail();

        return $new;
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
}
