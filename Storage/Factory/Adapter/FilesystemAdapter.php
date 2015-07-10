<?php namespace Burger\Storage\Factory\Adapter;

use Burger\IO\Filesystem\Exception\FileNotFoundException;
use Burger\Storage\Contract\StorageInterface;
use Burger\IO\Filesystem\File;
use Burger\IO\Filesystem\FilesystemFactory;
use Burger\Storage\Exception\StorageNotFoundException;
use Ramsey\Uuid\Uuid;

class FilesystemAdapter implements StorageInterface
{
    const DATAFOLDER = 'data/';

    public function __construct(array $config = array())
    {
        $factory = new FilesystemFactory();
        $basePath = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $slot = $config['slot'];
        $this->filesystem = $factory->create($basePath . '/datastorage/' . $slot);
        $this->mapper = new FilesystemMapper($this->filesystem, $slot);
    }

    public function has($reference)
    {
        if (!$this->mapper->has($reference)) {
            return $this->filesystem->has($this->getRealFileName($reference));
        }

        return true;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $arrayFiles = [];
        $files = $this->filesystem->getFiles(self::DATAFOLDER);
        foreach ($files as $file) {
            $fileStructure = [];
            $file = new File($this->filesystem, $file['path']);
            $fileStructure['reference'] = $file->getName();
            $fileStructure['content'] = $file->read();
            $arrayFiles[] = $fileStructure;
        }

        return $arrayFiles;
    }

    /**
     * @return bool
     */
    public function clearAll()
    {
        $this->mapper->clearAll();
        $files = array_filter($this->filesystem->getFiles(self::DATAFOLDER), function ($file) {
            return !$this->filesystem->delete($file['path']);
        });

        return empty($files) && is_array($files);
    }


    /**
     * @param $reference
     * @return mixed
     */
    public function delete($reference)
    {
        try {
            $this->mapper->delete($reference);

            return $this->filesystem->delete($this->getRealFileName($reference));
        } catch (FileNotFoundException $e) {
            return true;
        }
    }

    /**
     * @param $data
     * @param $reference
     * @return mixed
     */
    public function add($data, $reference = null)
    {
        if (is_null($reference)) {
            $reference = $this->generateReference();
        }
        $this->mapper->set($data, $reference);

        $this->filesystem->put($this->getRealFileName($reference), $data);

        return $reference;
    }

    /**
     * @param $reference
     * @param $data
     * @return mixed
     */
    public function update($reference, $data)
    {
        $this->mapper->update($reference, $data);

        return $this->filesystem->put($this->getRealFileName($reference), $data);
    }

    /**
     * @param $reference
     * @return mixed
     */
    public function get($reference)
    {
        try {
            return $this->filesystem->read($this->getRealFileName($reference));
        } catch (FileNotFoundException $e) {
            throw new StorageNotFoundException($reference);
        }
    }

    /**
     * @return mixed
     */
    public function getFirst()
    {
        $referenceOfFirstElement = $this->mapper->getFirst();

        return $this->get($referenceOfFirstElement);
    }

    /**
     * @param string $content
     * @return mixed
     */
    public function contains($content)
    {
        return $this->mapper->contains($content);
    }

    /**
     * @param $reference
     * @return mixed
     */
    private function getRealFileName($reference)
    {
        return self::DATAFOLDER . $reference;
    }

    /**
     * @return string
     */
    private function generateReference()
    {
        $uuid = Uuid::uuid4();

        return $uuid->toString();
    }
}
