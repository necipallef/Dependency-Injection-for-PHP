<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 20/03/2017
 * Time: 13:11
 */

namespace Fabs\DI;


class Service
{
    protected $service_name;
    protected $definition;
    protected $is_shared;

    protected $shared_instance;
    protected $parameters = [];

    public function __construct($service_name, $definition, $is_shared)
    {
        $this->service_name = $service_name;
        $this->definition = $definition;
        $this->is_shared = $is_shared;
    }

    public function getServiceName()
    {
        return $this->service_name;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    public function setParameters($parameters)
    {
        if (is_array($parameters)) {
            $this->parameters = $parameters;
        }
    }

    public function setIsShared($is_shared)
    {
        $this->is_shared = $is_shared;
    }

    public function isShared()
    {
        return $this->is_shared;
    }

    public function setDefinition($definition)
    {
        if ($this->definition != $definition) {
            $this->shared_instance = null;
        }
        $this->definition = $definition;
    }

    public function resolve($parameters = null)
    {
        $resolved = false;
        if ($this->is_shared) {
            if ($this->shared_instance != null) {
                return $this->shared_instance;
            }
        }

        $instance = null;

        if ($parameters == null || count($parameters) == 0 || $this->isShared()) {
            $parameters = $this->parameters;
        }

        if (is_string($this->definition)) {
            if (class_exists($this->definition)) {
                if ($parameters != null && count($parameters) > 0) {
                    $instance = new $this->definition(...$parameters);
                } else {
                    $instance = new $this->definition();
                }
                $resolved = true;
            }
        } else {
            if (is_callable($this->definition)) {
                if ($parameters != null && count($parameters) > 0) {
                    $instance = call_user_func_array($this->definition, $parameters);
                } else {
                    $instance = call_user_func($this->definition);
                }
                $resolved = true;
            } else if (is_object($this->definition)) {
                $instance = $this->definition;
                $resolved = true;
            }
        }

        if (!$resolved) {
            throw new \Exception('Service ' . $this->service_name . ' cannot be resolved');
        }

        if ($this->isShared()) {
            $this->shared_instance = $instance;
        }

        return $instance;
    }
}