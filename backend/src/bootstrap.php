<?php
declare(strict_types=1);

use App\DI\ContainerFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$diContainerFactory = new ContainerFactory();

return $diContainerFactory->create();
