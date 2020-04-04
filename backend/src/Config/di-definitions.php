<?php
declare(strict_types=1);

use App\Auth\ApiKeyRequestAuthenticator;
use App\Common\JSONDecoderMiddleware;
use App\Auth\RequestAuthenticatorInterface;
use App\Payment\Slim\PaymentSlimConfigurator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;

return [

    // Response factory
    ResponseFactoryInterface::class => function(ContainerInterface $container) {
        return $container->get(ResponseFactory::class);
    },

    // Authenticator
    RequestAuthenticatorInterface::class => function(ContainerInterface $container) {
        return $container->get(ApiKeyRequestAuthenticator::class);
    },

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