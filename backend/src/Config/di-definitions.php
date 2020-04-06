<?php
declare(strict_types=1);

use App\Auth\ApiKeyRequestAuthenticator;
use App\Common\JSONDecoderMiddleware;
use App\Auth\RequestAuthenticatorInterface;
use App\Logging\ResourceTrafficLogger;
use App\Logging\TrafficLoggerInterface;
use App\Logging\TrafficLogMiddleware;
use App\Payment\Slim\PaymentSlimConfigurator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;

const ENV_MONGO_URL = 'MONGO_URL';
const ENV_MONGO_DATABASE = 'MONGO_DATABASE';
const ENV_DEBUG = 'DEBUG';

return [

    // Api version
    'api.version'   =>  1,

    // Mongo constants
    'mongo.url' =>  DI\env(ENV_MONGO_URL, '127.0.0.1'),
    'mongo.database' =>  DI\env(ENV_MONGO_DATABASE, 'cashierDesk'),

    // Other settings
    'debug' => DI\env(ENV_DEBUG, false),

    // Mongo client factory
    \MongoDB\Database::class => function(ContainerInterface $container) {
        $client = new MongoDB\Client(
            $container->get('mongo.url')
        );
        return $client->selectDatabase($container->get('mongo.database'));
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

        // Setup logging
        $app->add(TrafficLogMiddleware::class);

        // Setup error reporting
        $app->addErrorMiddleware($container->get('debug') ? true : false, true, true);

        $app->setBasePath(sprintf('/api/v%d', $container->get('api.version')));

        return $app;
    },

    // Traffic logging
    TrafficLoggerInterface::class => DI\create(ResourceTrafficLogger::class)->constructor(fopen('php://stdout', 'w')),

    // Response factory
    ResponseFactoryInterface::class => function(ContainerInterface $container) {
        return $container->get(ResponseFactory::class);
    },

    // Authenticator
    RequestAuthenticatorInterface::class => function(ContainerInterface $container) {
        return $container->get(ApiKeyRequestAuthenticator::class);
    },
];