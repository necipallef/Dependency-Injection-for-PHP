<?php

/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 20/03/2017
 * Time: 13:06
 */
namespace Fabs\DI;

class DI implements \ArrayAccess
{
    protected static $defaultInstance;
    /**
     * @var Service[]
     */
    protected $services = [];

    /**
     * @return DI
     */
    public static function getDefault()
    {
        if (self::$defaultInstance == null) {
            self::$defaultInstance = new DI();
        }
        return self::$defaultInstance;
    }

    public function set($service_name, $definition, $shared = false)
    {
        $service = new Service($service_name, $definition, $shared);
        $this->services[$service_name] = $service;
        return $service;
    }

    public function setShared($service_name, $definition)
    {
        return $this->set($service_name, $definition, true);
    }

    public function remove($service_name)
    {
        unset($this->services[$service_name]);
    }

    /**
     * @param $service_name
     * @return Service
     * @throws \Exception
     */
    public function getService($service_name)
    {
        if (isset($this->services[$service_name])) {
            return $this->services[$service_name];
        }
        throw new \Exception("Service '" . $service_name . "' wasn't found in the dependency injection container");
    }

    public function get($service_name)
    {
        $resolved = $this->getService($service_name)->resolve();

        if ($resolved instanceof Injectable) {
            if (!$resolved->isServicesInjected()) {
                $resolved->setServicesInjected(true);

                foreach ($this->services as $service) {
                    $method_name = 'set ' . $service->getServiceName();

                    $method_name = str_replace(' ', '', ucwords(str_replace('-', ' ', str_replace('_', ' ', $method_name))));
                    $method_name[0] = strtolower($method_name[0]);

                    if (strpos($method_name, 'Service') === false) {
                        $method_name .= 'Service';
                    }
                    if (method_exists($resolved, $method_name)) {
                        $resolved->{$method_name}($this->get($service->getServiceName()));
                    }
                }
            }
        }
        return $resolved;
    }

    public function has($service_name)
    {
        return isset($this->services[$service_name]);
    }

    public function offsetExists($service_name)
    {
        return $this->has($service_name);
    }

    public function offsetGet($service_name)
    {
        return $this->get($service_name);
    }

    public function offsetSet($service_name, $definition)
    {
        $this->setShared($service_name, $definition);
        return true;
    }

    public function offsetUnset($offset)
    {
        return false;
    }
}