<?php
namespace App\DI;

use DI\Container;
use DI\ContainerBuilder;

/**
 * This class is used to create pre-configured container. Any other configuration steps can be added.
 */
class ContainerFactory
{
    /** @var callable[] Args: [ContainerBuilder $builder] */
    protected array $containerBuilderCallbacks = [];

    /**
     * ContainerFactory constructor.
     */
    public function __construct()
    {
        $this->containerBuilderCallbacks[] = function(ContainerBuilder $builder) {
            $builder->addDefinitions(__DIR__ . '/../Config/di-definitions.php');
        };
    }

    /**
     * Registered callbacks will be called when container is about to be built.
     * @param callable $callback Args: [ContainerBuilder $builder]
     */
    public function addContainerBuilderCallback(callable $callback) {
        $this->containerBuilderCallbacks[] = $callback;
    }

    /**
     * @return Container
     * @throws \Exception
     */
    public function create(): Container {
        $builder = new ContainerBuilder();
        foreach($this->containerBuilderCallbacks as $builderCallback) {
            $builderCallback($builder);
        }
        return $builder->build();
    }
}