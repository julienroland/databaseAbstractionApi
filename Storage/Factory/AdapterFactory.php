<?php namespace Burger\Storage\Factory;

class AdapterFactory
{
    private $factoryNamespace = '\\Adapter\\';


    /**
     * @param string $config
     * @param string $adapterClass
     * @return mixed
     * @throws \Exception
     */
    public function create($config = '', $adapterClass = 'FilesystemAdapter')
    {
        $classFactory = __NAMESPACE__ . $this->factoryNamespace . $adapterClass;
        if ($this->classExists($classFactory)) {
            $adapter = new $classFactory($config);
            return $adapter;
        }
        throw new \Exception("Class content adapter doesn't exist: {$adapterClass}");
    }

    /**
     * @param $classFactory
     * @return bool
     */
    private function classExists($classFactory)
    {
        return class_exists($classFactory);
    }
}
