<?php
declare(strict_types=1);

use App\Common\JSONDecoderMiddleware;
use App\Payment\Slim\PaymentSlimConfigurator;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

return [

    // App factory
    App::class => function (ContainerInterface $container) {

        // Create Slim app
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        // Setup payment REST requests handling
        $container->get(PaymentSlimConfigurator::class)->register($app);

        // Setup automatic input content decoding
        $app->add(JSONDecoderMiddleware::class);

        return $app;
    },
];