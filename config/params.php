<?php

use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Definitions\Reference;
use Mailery\Sender\Email\Model\EmailSenderType;
use Mailery\Sender\Email\Entity\EmailSender;

return [
    'yiisoft/yii-cycle' => [
        'entity-paths' => [
            '@vendor/maileryio/mailery-sender-email/src/Entity',
        ],
    ],

    'maileryio/mailery-activity-log' => [
        'entity-groups' => [
            'sender' => [
                'entities' => [
                    EmailSender::class,
                ],
            ],
        ],
    ],

    'maileryio/mailery-sender' => [
        'types' => [
            Reference::to(EmailSenderType::class),
        ],
    ],

    'maileryio/mailery-sender-email' => [
        'messageBodyTemplate' => [
            'viewPath' => '@vendor/maileryio/mailery-sender-email/resources/mail',
        ],
    ],

    'maileryio/mailery-menu-sidebar' => [
        'items' => [
            'senders' => [
                'items' => [
                    'email-addresses' => [
                        'label' => static function () {
                            return 'Email addresses';
                        },
                        'url' => static function (UrlGeneratorInterface $urlGenerator) {
                            return $urlGenerator->generate('/sender/email/index');
                        },
                        'activeRouteNames' => [
                            '/sender/email/index',
                            '/sender/email/view',
                            '/sender/email/create',
                            '/sender/email/edit',
                        ],
                    ],
                ],
                'activeRouteNames' => [
                    '/sender/email/index',
                    '/sender/email/view',
                    '/sender/email/create',
                    '/sender/email/edit',
                ],
            ],
        ],
    ],
];
