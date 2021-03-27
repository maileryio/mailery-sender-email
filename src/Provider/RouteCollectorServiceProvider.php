<?php

namespace Mailery\Sender\Email\Provider;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Sender\Email\Controller\DefaultController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function register(ContainerInterface $container): void
    {
        /** @var RouteCollectorInterface $collector */
        $collector = $container->get(RouteCollectorInterface::class);

        $collector->addGroup(
            Group::create(
                '/brand/{brandId:\d+}',
                [
                    // Senders:
                    Route::get('/sender/emails', [DefaultController::class, 'index'])
                        ->name('/sender/email/index'),
                    Route::get('/sender/email/view/{id:\d+}', [DefaultController::class, 'view'])
                        ->name('/sender/email/view'),
                    Route::methods(['GET', 'POST'], '/sender/email/create', [DefaultController::class, 'create'])
                        ->name('/sender/email/create'),
                    Route::methods(['GET', 'POST'], '/sender/email/edit/{id:\d+}', [DefaultController::class, 'edit'])
                        ->name('/sender/email/edit'),

                    Route::get('/sender/email/verify/{id:\d+}/{token:\w+}', [DefaultController::class, 'verify'])
                        ->name('/sender/email/verify'),
                ]
            )
        );
    }
}
