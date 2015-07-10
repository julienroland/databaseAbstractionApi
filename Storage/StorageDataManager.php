<?php namespace Burger\Storage;

use Burger\Storage\Contract\StorageDataManagerInterface;

class StorageDataManager implements StorageDataManagerInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function encode($data)
    {
        return serialize($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function decode($data)
    {
        if (!empty($data)) {
            return unserialize($data);
        }

        return $data;
    }
}
