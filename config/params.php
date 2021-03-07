<?php

use Yiisoft\Router\UrlGeneratorInterface;

return [
    'yiisoft/yii-cycle' => [
        'annotated-entity-paths' => [
            '@vendor/maileryio/mailery-sender-email/src/Entity',
        ],
    ],

    'maileryio/mailery-menu-sidebar' => [
        'items' => [
            'senders' => [
                'items' => [
                    'senders' => [
                        'label' => static function () {
                            return 'Email addresses';
                        },
                        'url' => static function (UrlGeneratorInterface $urlGenerator) {
                            return $urlGenerator->generate('/sender/email/index');
                        },
                    ],
                ],
            ],
        ],
    ],
];
