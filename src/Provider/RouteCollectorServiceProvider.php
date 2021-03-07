<?php

namespace Mailery\Sender\Email\Provider;

use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Sender\Email\Controller\SenderController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    /**
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        /** @var RouteCollectorInterface $collector */
        $collector = $container->get(RouteCollectorInterface::class);

        $collector->addGroup(
            Group::create(
                '/brand/{brandId:\d+}',
                [
                    // Senders:
                    Route::get('/senders', [SenderController::class, 'index'])
                        ->name('/sender/email/index'),
                    Route::get('/sender/email/view/{id:\d+}', [SenderController::class, 'view'])
                        ->name('/sender/email/view'),
                    Route::methods(['GET', 'POST'], '/sender/email/create', [SenderController::class, 'create'])
                        ->name('/sender/email/create'),
                    Route::methods(['GET', 'POST'], '/sender/email/edit/{id:\d+}', [SenderController::class, 'edit'])
                        ->name('/sender/email/edit'),
                    Route::delete('/sender/email/delete/{id:\d+}', [SenderController::class, 'delete'])
                        ->name('/sender/email/delete'),
                ]
            )
        );
    }
}
