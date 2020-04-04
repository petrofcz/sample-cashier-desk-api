<?php
declare(strict_types=1);

namespace App\Common;

use Slim\App;

/**
 * Interface for configuring Slim app instance.
 */
interface SlimConfiguratorInterface
{
    public function register(App $app);
}