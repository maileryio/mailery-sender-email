<?php

use Mailery\Sender\Email\Repository\SenderRepository;
use Psr\Container\ContainerInterface;
use Cycle\ORM\ORMInterface;
use Mailery\Sender\Email\Entity\Sender;

return [
    SenderRepository::class => static function (ContainerInterface $container) {
        return $container
            ->get(ORMInterface::class)
            ->getRepository(Sender::class);
    },
];
