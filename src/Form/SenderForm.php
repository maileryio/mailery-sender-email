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
    public function withSender(EmailSender $sender): self
    {
        $new = clone $this;
        $new->name = $sender->getName();
        $new->email = $sender->getEmail();
        $new->replyName = $sender->getReplyName();
        $new->replyEmail = $sender->getReplyEmail();

        return $new;
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
     * @return string
     */
    public function formName(): string
    {
        return 'SenderForm';
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'name' => [
                new RequiredHtmlOptions(new Required()),
                new HasLengthHtmlOptions((new HasLength())->max(255)),
            ],
            'email' => [
                new RequiredHtmlOptions(new Required()),
                new HasLengthHtmlOptions((new HasLength())->max(255)),
                new EmailHtmlOptions((new Email())),
            ],
            'replyName' => [
                new RequiredHtmlOptions(new Required()),
                new HasLengthHtmlOptions((new HasLength())->max(255)),
            ],
            'replyEmail' => [
                new RequiredHtmlOptions(new Required()),
                new HasLengthHtmlOptions((new HasLength())->max(255)),
                new EmailHtmlOptions((new Email())),
            ],
        ];
    }
}
