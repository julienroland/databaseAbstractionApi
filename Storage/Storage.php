<?php namespace Burger\Storage;

use Burger\Storage\Contract\StorageInterface;
use Burger\Storage\Factory\AdapterFactory;

class Storage implements StorageInterface
{
    protected $slot;

    /**
     * @var mixed
     */
    private $adapter;
    /**
     * @var bool
     */
    protected $isOnlyMapping;

    /**
     * @param $slotName
     * @throws \Exception
     */
    public function __construct($slotName)
    {
        $adapterFactory = new AdapterFactory();
        $this->slot = strtolower($slotName);
        $config = [
            'dbname' => 'burger',
            'slot' => $this->slot,
            'user' => 'vagrant',
            'password' => 'vagrant',
            'host' => '10.0.1.21',
            'driver' => 'pdo_mysql',
        ];

        $this->adapter = $adapterFactory->create($config);
        $this->data = new StorageDataManager();
    }

    /**
     * @param $data
     * @param $reference
     * @return string $reference
     */
    public function add($data, $reference = null)
    {
        $reference = $this->adapter->add($this->data->encode($data), $reference);

        return $reference;
    }

    /**
     * @param $reference
     * @param $data
     * @return boolean
     */
    public function update($reference, $data)
    {
        if ($this->has($reference)) {
            $encode = $this->data->encode($data);
            return $this->adapter->update($reference, $encode);
        }

        return false;
    }

    /**
     * @param $reference
     * @return boolean
     */
    public function has($reference)
    {
        return $this->adapter->has($reference);
    }

    /**
     * @param $content
     * @return boolean
     */
    public function contains($content)
    {
        return $this->adapter->contains($this->data->encode($content));
    }

    /**
     * @param $reference
     * @return mixed
     */
    public function get($reference)
    {
        return $this->data->decode($this->adapter->get($reference));
    }

    /**
     * @return mixed
     */
    public function getFirst()
    {
        return $this->data->decode($this->adapter->getFirst());
    }

    /**
     * @param $reference
     * @return boolean
     */
    public function delete($reference)
    {
        return $this->adapter->delete($reference);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->adapter->getAll();
    }

    /**
     * @return boolean
     */
    public function clearAll()
    {
        return $this->adapter->clearAll();
    }
}
