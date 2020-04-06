<?php
declare(strict_types=1);

use Slim\App;

/** @var \DI\Container $container */
$container = require __DIR__ . '/../src/bootstrap.php';

// Create Slim app
$app = $container->get(App::class);

$app->run();