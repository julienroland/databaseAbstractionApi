<?php namespace Burger\Storage\Test\PhpUnit;

use Burger\Storage\Factory\AdapterFactory;

class AdapterFactoryTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->adapter = $this->getAdapter();
    }

    public function testCreateInexistantAdapter()
    {
        $this->setExpectedException('\Exception');
        $this->adapter->create('', 'BadAdapter');
    }

    public function testCreateGoodAdapter()
    {
        $this->assertInstanceOf(
            'Burger\Storage\Factory\Adapter\FilesystemAdapter',
            $this->adapter->create(['slot' => 'test'], 'FilesystemAdapter')
        );
    }

    protected function getAdapter()
    {
        return new AdapterFactory();
    }
}
