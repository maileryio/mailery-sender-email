<?php

declare(strict_types=1);

use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Sender\Email\Controller\DefaultController;
use Mailery\Sender\Email\Controller\VerifyController;

return [
    Group::create('/brand/{brandId:\d+}')
        ->routes(
            // Senders:
            Route::get('/sender/emails')
                ->name('/sender/email/index')
                ->action([DefaultController::class, 'index']),
            Route::get('/sender/email/view/{id:\d+}')
                ->name('/sender/email/view')
                ->action([DefaultController::class, 'view']),
            Route::methods(['GET', 'POST'], '/sender/email/create')
                ->name('/sender/email/create')
                ->action([DefaultController::class, 'create']),
            Route::methods(['GET', 'POST'], '/sender/email/edit/{id:\d+}')
                ->name('/sender/email/edit')
                ->action([DefaultController::class, 'edit']),
            Route::delete('/sender/email/delete/{id:\d+}')
                ->name('/sender/email/delete')
                ->action([DefaultController::class, 'delete']),

            Route::get('/sender/email/verify/{id:\d+}/{token:\w+}')
                ->name('/sender/email/verify')
                ->action([VerifyController::class, 'index'])
    )
];
