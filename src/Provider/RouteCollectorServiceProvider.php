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

                    Route::get('/sender/email/verify/{id:\d+}/{token:\w+}')
                        ->name('/sender/email/verify')
                        ->action([DefaultController::class, 'verify'])
            )
        );
    }
}
