<?php
namespace Burger\Storage\Contract;

interface StorageDataManagerInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function encode($data);

    /**
     * @param $data
     * @return mixed
     */
    public function decode($data);
}
