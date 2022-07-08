<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Service;

use Cycle\ORM\EntityManagerInterface;
use Mailery\Sender\Field\SenderStatus;
use Mailery\Sender\Email\Entity\EmailSender;
use Mailery\Sender\Email\ValueObject\SenderValueObject;
use Mailery\Brand\Entity\Brand;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class SenderCrudService
{
    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * @param Brand $brand
     * @return self
     */
    public function withBrand(Brand $brand): self
    {
        $new = clone $this;
        $new->brand = $brand;

        return $new;
    }

    /**
     * @param SenderValueObject $valueObject
     * @return EmailSender
     */
    public function create(SenderValueObject $valueObject): EmailSender
    {
        $sender = (new EmailSender())
            ->setBrand($this->brand)
            ->setChannel($valueObject->getChannel())
            ->setName($valueObject->getName())
            ->setEmail($valueObject->getEmail())
            ->setReplyName($valueObject->getReplyName())
            ->setReplyEmail($valueObject->getReplyEmail())
            ->setDescription($valueObject->getDescription())
            ->setStatus(SenderStatus::asPending())
        ;

        (new EntityWriter($this->entityManager))->write([$sender]);

        return $sender;
    }

    /**
     * @param EmailSender $sender
     * @param SenderValueObject $valueObject
     * @return EmailSender
     */
    public function update(EmailSender $sender, SenderValueObject $valueObject): EmailSender
    {
        $sender = $sender
            ->setName($valueObject->getName())
            ->setReplyName($valueObject->getReplyName())
            ->setReplyEmail($valueObject->getReplyEmail())
            ->setDescription($valueObject->getDescription())
        ;

        (new EntityWriter($this->entityManager))->write([$sender]);

        return $sender;
    }

    /**
     * @param EmailSender $sender
     * @return bool
     */
    public function delete(EmailSender $sender): bool
    {
        (new EntityWriter($this->entityManager))->delete([$sender]);

        return true;
    }
}
