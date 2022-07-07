<?php

use Mailery\Sender\Email\Service\SenderVerifyService;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Mailer\MessageBodyTemplate;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Definitions\DynamicReference;
use Mailery\Channel\Email\Amazon\Repository\CredentialsRepository;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Mailery\Channel\Email\Amazon\Entity\Credentials;

return [
    SenderVerifyService::class => [
        '__construct()' => [
            'mailer' => DynamicReference::to(static function (MailerInterface $mailer, Aliases $aliases) use($params) {
                return $mailer->withTemplate(
                    new MessageBodyTemplate(
                        $aliases->get($params['maileryio/mailery-sender-email']['messageBodyTemplate']['viewPath']),
                        '',
                        ''
                    )
                );
            }),
        ],
    ],

    CredentialsRepository::class => static function (ContainerInterface $container) {
        return $container
            ->get(ORMInterface::class)
            ->getRepository(Credentials::class);
    },
];
