<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Form;

use Yiisoft\Form\FormModel;
use Mailery\Sender\Email\Entity\EmailSender;
use Mailery\Sender\Email\Model\EmailSenderType;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Result;
use Mailery\Sender\Repository\SenderRepository;
use Mailery\Brand\BrandLocatorInterface as BrandLocator;
use Mailery\Channel\Entity\Channel;
use Mailery\Channel\Repository\ChannelRepository;

class SenderForm extends FormModel
{
    /**
     * @var string|null
     */
    private ?int $channel = null;

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
     * @var string|null
     */
    private ?string $description = null;

    /**
     * @var EmailSender|null
     */
    private ?EmailSender $entity = null;

    /**
     * @param SenderRepository $senderRepo
     * @param ChannelRepository $channelRepo
     * @param EmailSenderType $senderType
     * @param BrandLocator $brandLocator
     */
    public function __construct(
        private SenderRepository $senderRepo,
        private ChannelRepository $channelRepo,
        EmailSenderType $senderType,
        BrandLocator $brandLocator
    ) {
        $this->senderRepo = $senderRepo->withBrand($brandLocator->getBrand());
        $this->channelRepo = $channelRepo->withBrand($brandLocator->getBrand())
            ->withChannelTypes(...$senderType->getAvailChannelTypes());

        parent::__construct();
    }

    /**
     * @return bool
     */
    public function hasEntity(): bool
    {
        return $this->entity !== null;
    }

    /**
     * @param EmailSender $entity
     * @return self
     */
    public function withEntity(EmailSender $entity): self
    {
        $new = clone $this;
        $new->entity = $entity;
        $new->channel = $entity->getChannel()?->getId();
        $new->name = $entity->getName();
        $new->email = $entity->getEmail();
        $new->replyName = $entity->getReplyName();
        $new->replyEmail = $entity->getReplyEmail();
        $new->description = $entity->getDescription();

        return $new;
    }

    /**
     * @return Channel|null
     */
    public function getChannel(): ?Channel
    {
        if ($this->channel === null) {
            return null;
        }

        return $this->channelRepo->findByPK($this->channel);
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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getAttributeLabels(): array
    {
        return [
            'channel' => 'Channel',
            'name' => 'Name',
            'email' => 'Email',
            'replyName' => 'Reply to name',
            'replyEmail' => 'Reply to email',
            'description' => 'Description (optional)',
        ];
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'channel' => [
                Required::rule(),
                InRange::rule(array_keys($this->getChannelListOptions())),
            ],
            'name' => [
                Required::rule(),
                HasLength::rule()->min(3)->max(255),
                Callback::rule(function ($value) {
                    $result = new Result();

                    if (!empty($value)) {
                        $record = $this->senderRepo->findByAttribute('name', $value, $this->entity);
                        if ($record !== null) {
                            $result->addError('Sender with this name already exists.');
                        }
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

                    if (!empty($value)) {
                        $record = $this->senderRepo->findByAttribute('email', $value, $this->entity);
                        if ($record !== null) {
                            $result->addError('Sender with this email already exists.');
                        }
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

    /**
     * @return array
     */
    public function getChannelListOptions(): array
    {
        $options = [];
        $channels = $this->channelRepo->findAll();

        foreach ($channels as $channel) {
            $options[$channel->getId()] = $channel->getName();
        }

        return $options;
    }

}
