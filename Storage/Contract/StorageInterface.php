<?php
namespace Burger\Storage\Contract;

interface StorageInterface
{
    /**
     * @param $data
     * @param $reference
     * @return boolean
     */
    public function add($data, $reference);

    /**
     * @param $reference
     * @param $data
     * @return boolean
     */
    public function update($reference, $data);

    /**
     * @param $reference
     * @return boolean
     */
    public function has($reference);

    /**
     * @param $content
     * @return boolean
     */
    public function contains($content);

    /**
     * @param $reference
     * @return string
     */
    public function get($reference);

    /**
     * @return string
     */
    public function getFirst();

    /**
     * @param $reference
     * @return boolean
     */
    public function delete($reference);

    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @return mixed
     */
    public function clearAll();
}
