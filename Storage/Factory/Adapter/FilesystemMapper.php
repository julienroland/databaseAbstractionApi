<?php namespace Burger\Storage\Factory\Adapter;

use Burger\IO\Filesystem\Contract\FilesystemInterface;
use Burger\Storage\Contract\StorageMapperInterface;

class FilesystemMapper implements StorageMapperInterface
{
    const EXTENSION = '.json';
    const BASEFILE = '{}';
    /**
     * @var
     */
    private $slotName;
    /**
     * @var
     */
    private $mapper;

    public function __construct(FilesystemInterface $filesystem, $slotName)
    {
        $this->filesystem = $filesystem;
        $this->slotName = $slotName;
        $this->mapper = $this->load();
    }

    /**
     * @return mixed
     */
    public function load()
    {
        if ($this->filesystem->has($this->getMapperFileFullName())) {
            return json_decode($this->filesystem->read($this->getMapperFileFullName()), true);
        }

        return $this->createFile($this->getMapperFileFullName());
    }

    /**
     * @param $data
     * @param $reference
     * @return bool
     */
    public function set($data, $reference)
    {
        $this->mapper[$reference] = $this->computeControlSum($data);
        $this->save();

        return $this;
    }

    /**
     * @return bool
     */
    public function save()
    {
        return $this->filesystem->put($this->getMapperFileFullName(), json_encode($this->mapper));
    }

    /**
     * @param $reference
     * @param $data
     * @return bool
     */
    public function update($reference, $data)
    {
        if ($this->has($reference)) {
            $this->mapper[$reference] = $this->computeControlSum($data);
            $this->save();

            return $this;
        }
        throw new \InvalidArgumentException($reference);
    }

    /**
     * @param $reference
     * @return bool
     */
    public function has($reference)
    {
        return $this->keyExists($reference);
    }

    /**
     * @param $reference
     * @return bool
     */
    public function delete($reference)
    {
        if ($this->has($reference)) {
            unset($this->mapper[$reference]);
        }
        $this->save();

        return $this;
    }

    /**
     * @return mixed
     */
    public function destroy()
    {
        $isDeleted = true;
        if ($this->filesystem->has($this->getMapperFileFullName())) {
            $isDeleted = $this->filesystem->delete($this->getMapperFileFullName());
            $this->mapper = null;
        }

        return $isDeleted;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return array_keys($this->mapper);
    }

    /**
     * @return mixed
     */
    public function getFirst()
    {
        $mapperKeys = array_keys($this->mapper);

        return array_shift($mapperKeys);
    }

    /**
     * @return bool
     */
    public function clearAll()
    {
        $this->mapper = [];
        $this->save();

        return $this;
    }

    /**
     * @param string $data
     * @return boolean
     */
    public function contains($data)
    {
        $controlSum = $this->computeControlSum($data);

        return array_search($controlSum, $this->mapper) !== false ? true : false;
    }

    /**
     * @param $data
     * @return string
     */
    private function computeControlSum($data)
    {
        return sha1($data);
    }

    /**
     * @param $reference
     * @param $container
     * @return bool
     */
    private function keyExists($reference, $container = null)
    {
        if (is_null($container)) {
            $container = $this->mapper;
        }

        return array_key_exists($reference, $container);
    }

    /**
     * @param $filename
     * @return mixed
     */
    private function createFile($filename)
    {
        $this->filesystem->put($filename, self::BASEFILE);

        return $this->load($this->slotName);
    }

    /**
     * @return string
     */
    private function getMapperFileFullName()
    {
        return $this->slotName . self::EXTENSION;
    }
}
