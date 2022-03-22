<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Form;

use Yiisoft\Form\FormModel;
use Mailery\Sender\Email\Entity\EmailSender;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Result;
use Mailery\Sender\Repository\SenderRepository;
use Mailery\Brand\BrandLocatorInterface as BrandLocator;

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
     * @var EmailSender|null
     */
    private ?EmailSender $sender = null;

    /**
     * @param SenderRepository $senderRepo
     * @param BrandLocator $brandLocator
     */
    public function __construct(
        private SenderRepository $senderRepo,
        BrandLocator $brandLocator
    ) {
        $this->senderRepo = $senderRepo->withBrand($brandLocator->getBrand());
        parent::__construct();
    }

    /**
     * @param EmailSender $sender
     * @return self
     */
    public function withEntity(EmailSender $sender): self
    {
        $new = clone $this;
        $new->sender = $sender;
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
                Required::rule(),
                HasLength::rule()->min(3)->max(255),
                Callback::rule(function ($value) {
                    $result = new Result();
                    $record = $this->senderRepo->findByAttribute('name', $value, $this->sender);

                    if ($record !== null) {
                        $result->addError('Sender with this name already exists.');
                    }

                    return $result;
                }),
            ],
            'email' => [
                Required::rule(),
                Email::rule(),
                HasLength::rule()->max(255),
                Callback::rule(function ($value) {
                    $result = new Result();
                    $record = $this->senderRepo->findByAttribute('email', $value, $this->sender);

                    if ($record !== null) {
                        $result->addError('Sender with this email already exists.');
                    }

                    return $result;
                }),
            ],
            'replyName' => [
                Required::rule(),
                HasLength::rule()->min(3)->max(255),
            ],
            'replyEmail' => [
                Required::rule(),
                Email::rule(),
                HasLength::rule()->max(255),
            ],
        ];
    }
}
