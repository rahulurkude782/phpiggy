<?php

declare(strict_types=1);

namespace Framework;

use Framework\Exceptions\ContainerException;
use ReflectionClass, ReflectionNamedType;

use function App\dd;

class Container
{
    private array $definitions = [];

    /* Singleton Pattern use case. */
    private array $resolvedDependencies = [];

    public function addDefinitions(array $newDefinitions)
    {
        $this->definitions = [...$this->definitions, ...$newDefinitions];
    }

    public function resolveDependency(string $className)
    {
        $reflectionClass = new ReflectionClass($className);

        /* Check or validate whether the class is instantiable. */

        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException("Class {$className} is not instantiable.");
        }

        /* Get the __construct method to peek into the parameters. */

        $construct = $reflectionClass->getConstructor();

        /* If there is no __construct method return the instance of class right-away. */

        if (!$construct) {
            return new $className();
        }

        /* Get the parameters of __construct method. */

        $params = $construct->getParameters();

        if (count($params) === 0) {
            return new $className();
        }

        $dependencies = [];

        foreach ($params as $param) {
            $name = $param->getName();
            $type = $param->getType();

            if (!$type) {
                throw new ContainerException("Failed to resolve class {$className} because param {$name} has missing type hint.");
            }

            /* Validates whether the param is built-in type */
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new ContainerException("Failed to resolve class {$className} because invalid param name.");
            }

            $dependencies[] = $this->get($type->getName());
        }
        return $reflectionClass->newInstanceArgs($dependencies);
    }

    public function get(string $id)
    {
        if (!array_key_exists($id, $this->definitions)) {
            throw new ContainerException("Class {$id} does not exists in container.");
        }

        if (array_key_exists($id, $this->resolvedDependencies)) {
            return $this->resolvedDependencies[$id];
        }

        $factory = $this->definitions[$id];

        /* For manual dep-injection $this is passed to factory functions. */
        $dependency = $factory($this);

        $this->resolvedDependencies[$id] = $dependency;

        return $dependency;
    }
}
