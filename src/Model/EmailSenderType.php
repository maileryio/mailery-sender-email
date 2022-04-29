<?php

namespace Mailery\Sender\Email\Model;

use Mailery\Sender\Entity\Sender;
use Mailery\Sender\Model\SenderTypeInterface;
use Mailery\Channel\Model\ChannelTypeInterface as ChannelType;
use Mailery\Channel\Amazon\Ses\Model\AmazonSesChannelType;
use Mailery\Channel\Smtp\Model\SmtpChannelType;

class EmailSenderType implements SenderTypeInterface
{

    /**
     * @param SmtpChannelType $smtpChannelType
     * @param AmazonSesChannelType $amazonSesChannelType
     */
    public function __construct(
        private SmtpChannelType $smtpChannelType,
        private AmazonSesChannelType $amazonSesChannelType
    ) {}

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return self::class;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return 'Email address';
    }

    /**
     * @return string
     */
    public function getCreateLabel(): string
    {
        return 'Email address';
    }

    /**
     * @return string|null
     */
    public function getCreateRouteName(): ?string
    {
        return '/sender/email/create';
    }

    /**
     * @return array
     */
    public function getCreateRouteParams(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAvailChannelTypes(): array
    {
        return [
            $this->smtpChannelType,
            $this->amazonSesChannelType,
        ];
    }

    /**
     * @param Sender $entity
     * @return bool
     */
    public function isEntitySameType(Sender $entity): bool
    {
        return $entity->getType() === $this->getName();
    }

    /**
     * @param ChannelType $channelType
     * @return bool
     */
    public function canUseChannelType(ChannelType $channelType): bool
    {
        return in_array($channelType, $this->getAvailChannelTypes(), true);
    }

}
