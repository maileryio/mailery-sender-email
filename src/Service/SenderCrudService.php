<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Service;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Mailery\Sender\Email\Entity\EmailSender;
use Mailery\Sender\Email\ValueObject\SenderValueObject;
use Mailery\Brand\Entity\Brand;

class SenderCrudService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @param ORMInterface $orm
     */
    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

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
            ->setName($valueObject->getName())
            ->setEmail($valueObject->getEmail())
            ->setReplyName($valueObject->getReplyName())
            ->setReplyEmail($valueObject->getReplyEmail())
            ->setStatus(EmailSender::STATUS_PENDING)
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($sender);
        $tr->run();

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
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($sender);
        $tr->run();

        return $sender;
    }
}
