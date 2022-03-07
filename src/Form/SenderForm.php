<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Form;

use Yiisoft\Form\FormModel;
use Mailery\Sender\Email\Entity\EmailSender;
use Yiisoft\Form\HtmlOptions\RequiredHtmlOptions;
use Yiisoft\Form\HtmlOptions\HasLengthHtmlOptions;
use Yiisoft\Form\HtmlOptions\EmailHtmlOptions;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Email;

class SenderForm extends FormModel
{
    /**
     * @var string|null
     */
    private ?string $name = null;

    /**
     * @var string|null
     */
    private ?string $email = null;

    /**
     * @var string|null
     */
    private ?string $replyName = null;

    /**
     * @var string|null
     */
    private ?string $replyEmail = null;

    /**
     * @param EmailSender $sender
     * @return self
     */
    public function withEntity(EmailSender $sender): self
    {
        $new = clone $this;
        $new->name = $sender->getName();
        $new->email = $sender->getEmail();
        $new->replyName = $sender->getReplyName();
        $new->replyEmail = $sender->getReplyEmail();

        return $new;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getReplyName(): ?string
    {
        return $this->replyName;
    }

    /**
     * @return string|null
     */
    public function getReplyEmail(): ?string
    {
        return $this->replyEmail;
    }

    /**
     * @return array
     */
    public function getAttributeLabels(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'replyName' => 'Reply to name',
            'replyEmail' => 'Reply to email',
        ];
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'name' => [
                new RequiredHtmlOptions(Required::rule()),
                new HasLengthHtmlOptions(HasLength::rule()->min(3)->max(255)),
            ],
            'email' => [
                new RequiredHtmlOptions(Required::rule()),
                new EmailHtmlOptions((Email::rule())),
                new HasLengthHtmlOptions(HasLength::rule()->max(255)),
            ],
            'replyName' => [
                new RequiredHtmlOptions(Required::rule()),
                new HasLengthHtmlOptions(HasLength::rule()->min(3)->max(255)),
            ],
            'replyEmail' => [
                new RequiredHtmlOptions(Required::rule()),
                new EmailHtmlOptions((Email::rule())),
                new HasLengthHtmlOptions(HasLength::rule()->max(255)),
            ],
        ];
    }
}
