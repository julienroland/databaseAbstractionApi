<?php namespace Burger\Storage;

use Burger\Storage\Contract\StorageInterface;

class ProxyStorage implements StorageInterface
{
    static private $storage = [];
    /**
     * @var
     */

    private $slotName;

    /**
     * @param $slotName
     */
    public function __construct($slotName)
    {
        $this->slotName = $slotName;
    }

    /**
     * @param $data
     * @param $reference
     * @return string $reference
     */
    public function add($data, $reference = null)
    {
        $storage = $this->getStorage();

        return $storage->add($data, $reference);
    }

    /**
     * @param $reference
     * @param $data
     * @return boolean
     */
    public function update($reference, $data)
    {
        $storage = $this->getStorage();

        return $storage->update($reference, $data);
    }

    /**
     * @param $reference
     * @return boolean
     */
    public function has($reference)
    {
        $storage = $this->getStorage();

        return $storage->has($reference);
    }

    /**
     * @param $content
     * @return boolean
     */
    public function contains($content)
    {
        $storage = $this->getStorage();

        return $storage->contains($content);
    }

    /**
     * @param $reference
     * @return mixed
     */
    public function get($reference)
    {
        $storage = $this->getStorage();

        return $storage->get($reference);
    }

    /**
     * @return mixed
     */
    public function getFirst()
    {
        $storage = $this->getStorage();

        return $storage->getFirst();
    }

    /**
     * @param $reference
     * @return boolean
     */
    public function delete($reference)
    {
        $storage = $this->getStorage();

        return $storage->delete($reference);
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        $storage = $this->getStorage();

        return $storage->getAll();
    }

    /**
     * @return boolean
     */
    public function clearAll()
    {
        $storage = $this->getStorage();

        return $storage->clearAll();
    }

    /* *
  * Proxy methods
  * manage instanciation
  * */

    /**
     * @return StorageInterface
     */
    private function getStorage()
    {
        if (empty(self::$storage[$this->slotName])) {
            $this->createStorage();
        }

        return self::$storage[$this->slotName];
    }

    /**
     *
     */
    private function createStorage()
    {
        self::$storage[$this->slotName] = new Storage($this->slotName);
    }
}
