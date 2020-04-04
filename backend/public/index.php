<?php
declare(strict_types=1);

use Slim\App;

// todo vymysli ini_set('display_errors', 1); - nejlepe v dockeru

/** @var \DI\Container $container */
$container = require __DIR__ . '../src/bootstrap.php';

// Create Slim app
$app = $container->get(App::class);

$app->run();