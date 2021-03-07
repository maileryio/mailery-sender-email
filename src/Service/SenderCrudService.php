<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Service;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Mailery\Sender\Email\Entity\Sender;
use Mailery\Sender\Email\ValueObject\SenderValueObject;

class SenderCrudService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @param ORMInterface $orm
     */
    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    /**
     * @param SenderValueObject $valueObject
     * @return Sender
     */
    public function create(SenderValueObject $valueObject): Sender
    {
        $sender = (new Sender())
            ->setName($valueObject->getName())
            ->setBrand($valueObject->getBrand())
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($sender);
        $tr->run();

        return $sender;
    }

    /**
     * @param Sender $sender
     * @param SenderValueObject $valueObject
     * @return Sender
     */
    public function update(Sender $sender, SenderValueObject $valueObject): Sender
    {
        $sender = $sender
            ->setName($valueObject->getName())
            ->setBrand($valueObject->getBrand())
        ;

        $tr = new Transaction($this->orm);
        $tr->persist($sender);
        $tr->run();

        return $sender;
    }

    /**
     * @param Sender $sender
     * @return bool
     */
    public function delete(Sender $sender): bool
    {
        $tr = new Transaction($this->orm);
        $tr->delete($sender);
        $tr->run();

        return true;
    }
}
