<?php

namespace Celovel\Support;

class ServiceContainer
{
    protected array $services = [];
    protected array $singletons = [];

    public function bind(string $abstract, $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->services[$abstract] = $concrete;
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->singletons[$abstract] = $concrete;
    }

    public function make(string $abstract)
    {
        // Singleton kontrolü
        if (isset($this->singletons[$abstract])) {
            if (!isset($this->services[$abstract])) {
                $this->services[$abstract] = $this->build($this->singletons[$abstract]);
            }
            return $this->services[$abstract];
        }

        // Normal binding
        if (isset($this->services[$abstract])) {
            return $this->build($this->services[$abstract]);
        }

        // Class varsa direkt oluştur
        if (class_exists($abstract)) {
            return $this->build($abstract);
        }

        throw new \Exception("Service [{$abstract}] not found.");
    }

    protected function build($concrete)
    {
        if (is_callable($concrete)) {
            return $concrete();
        }

        if (is_string($concrete) && class_exists($concrete)) {
            $reflection = new \ReflectionClass($concrete);
            
            if (!$reflection->isInstantiable()) {
                throw new \Exception("Class [{$concrete}] is not instantiable.");
            }

            $constructor = $reflection->getConstructor();
            
            if ($constructor === null) {
                return new $concrete;
            }

            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $type = $parameter->getType();
                
                if ($type && !$type->isBuiltin()) {
                    $dependencies[] = $this->make($type->getName());
                } else {
                    $dependencies[] = $parameter->getDefaultValue();
                }
            }

            return $reflection->newInstanceArgs($dependencies);
        }

        return $concrete;
    }

    public function has(string $abstract): bool
    {
        return isset($this->services[$abstract]) || isset($this->singletons[$abstract]);
    }
}
