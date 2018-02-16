<?php

namespace Fabs\DI;

class ServiceDefinition
{
    /** @var string */
    private $service_name = null;
    /** @var bool */
    private $shared = false;
    /** @var mixed */
    private $instance = null;
    /** @var mixed[] */
    private $parameters = null;
    /** @var string|callable */
    private $definition = null;
    /** @var Container */
    private $container = null;

    /**
     * ServiceDefinition constructor.
     * @param Container $container
     * @param string $service_name
     * @param string|callable|mixed $definition
     * @param bool $shared
     * @param mixed[] $parameters
     */
    public function __construct($container, $service_name, $definition, $shared, $parameters = [])
    {
        $this->container = $container;
        $this->service_name = $service_name;
        $this->setDefinition($definition);
        $this->shared = $shared;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * @return bool
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function isShared()
    {
        return $this->shared;
    }

    /**
     * @param bool $is_shared
     * @return ServiceDefinition
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function setShared($is_shared)
    {
        $this->shared = $is_shared;
        return $this;
    }

    /**
     * @param string|callable|mixed $definition
     * @return ServiceDefinition
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function setDefinition($definition)
    {
        if ($this->getDefinition() != $definition) {
            $this->setInstance(null);
        }
        $this->definition = $definition;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        if ($this->isShared()) {
            if ($this->instance !== null) {
                return $this->instance;
            }
        }

        $instance = $this->container->createInstance($this->getDefinition(), $this->parameters);

        if ($this->isShared()) {
            $this->instance = $instance;
        }

        return $instance;
    }

    /**
     * @param mixed $instance
     * @return static
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * @return callable|mixed|string
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function getDefinition()
    {
        return $this->definition;
    }
}
