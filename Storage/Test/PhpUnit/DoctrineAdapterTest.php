<?php namespace Burger\Storage\Test\PhpUnit;

use Burger\Storage\Factory\AdapterFactory;
use Burger\Storage\ProxyStorage;

class DoctrineAdapterTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->doctrine = $this->getDoctrine();
        $this->content = 'test content';
        $this->reference = $this->doctrine->add($this->content);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->doctrine->clearAll();
    }

    public function testCanConnectToDb()
    {
        $this->assertInstanceOf('\\Burger\\Storage\\Contract\\StorageInterface', $this->doctrine);
        $this->assertTrue($this->doctrine->isConnected());
    }

//    public function testCantConnectionWithBadCredentials()
//    {
//        $storageFactory = new AdapterFactory();
//        $config = array(
//            'dbname' => 'badName',
//            'user' => 'baduser',
//            'slot' => 'test',
//            'password' => 'badpassword',
//            'host' => 'localhost',
//            'driver' => 'pdo_mysql',
//        );
//
//        $doctrine = $storageFactory->create($config, 'DoctrineAdapter');
//        $this->assertInstanceOf('\\Burger\\Storage\\Contract\\StorageInterface', $doctrine);
//        $this->assertFalse($doctrine->isConnected());
//    }

    public function testAddData()
    {
        $this->doctrine->add($this->content);
        $this->assertEquals($this->content, $this->doctrine->get($this->reference));
        $this->assertEquals(1111, $this->doctrine->add($this->content, 1111));
    }

    public function testGetData()
    {
        $this->assertEquals($this->content, $this->doctrine->get($this->reference));
        $this->setExpectedException('Burger\Storage\Exception\StorageNotFoundException');
        $this->doctrine->get('badReference');
    }

    public function testHasData()
    {
        $this->assertTrue($this->doctrine->has($this->reference));
        $this->assertFalse($this->doctrine->has('badReference'));
    }

    public function testUpdateData()
    {
        $updatedContent = 'updated content';
        $this->assertTrue($this->doctrine->update($this->reference, $updatedContent));
        $this->assertEquals($this->doctrine->get($this->reference), $updatedContent);
    }

    public function testContainsData()
    {
        $this->assertTrue($this->doctrine->contains($this->content));
        $this->assertFalse($this->doctrine->contains('badContent'));
    }

    public function testGetFirstData()
    {
        $this->doctrine->add($this->content);
        $this->doctrine->add($this->content);
        $this->assertEquals($this->doctrine->getFirst(), $this->content);
    }

    public function testClearAllData()
    {
        $this->assertTrue($this->doctrine->clearAll());
        $this->assertFalse($this->doctrine->has($this->reference));
    }

    public function testDeleteData()
    {
        $this->assertTrue($this->doctrine->delete($this->reference));
        $this->assertTrue($this->doctrine->delete($this->reference));
    }

    public function testGetAll()
    {
        $this->assertInternalType('array', $this->doctrine->getAll());
        $this->assertCount(1, $this->doctrine->getAll());
        $this->doctrine->add($this->content);
        $this->doctrine->add($this->content);
        $this->assertCount(3, $this->doctrine->getAll());
        $this->assertArrayHasKey('reference', $this->doctrine->getAll()[0]);
        $this->assertArrayHasKey('content', $this->doctrine->getAll()[0]);
    }

    private function getDoctrine()
    {
        $storageFactory = new AdapterFactory();
        $config = [
            'dbname' => 'burger',
            'slot' => 'test',
            'user' => 'vagrant',
            'password' => 'vagrant',
            'host' => '10.0.1.21',
            'driver' => 'pdo_mysql',
        ];

        return $storageFactory->create($config, 'DoctrineAdapter');
    }

}
