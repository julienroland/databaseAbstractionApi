<?php namespace Burger\Storage\Test\PhpUnit;

use Burger\Storage\ProxyStorage;

class StorageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->content = 'metus quis porttitor rhoncus, turpis sapien vulputate';
        $this->storage = $this->getStorage();
        $this->reference = $this->storage->add($this->content);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->storage->clearAll();
    }

    public function testAddData()
    {
        $reference = $this->storage->add($this->content);
        $this->assertInternalType('string', $reference);
        $this->assertEquals($this->content, $this->storage->get($reference));
    }

    public function testUpdateData()
    {
        $this->assertTrue($this->storage->update($this->reference, 'content'));
        $this->assertEquals('content', $this->storage->get($this->reference));
        $this->assertFalse($this->storage->update('badReference', $this->content));
    }

    public function testHasData()
    {
        $this->assertTrue($this->storage->has($this->reference));
        $this->assertFalse($this->storage->has('notTheGoodReference'));
    }

    public function testContainsData()
    {
        $this->assertTrue($this->storage->contains($this->content));
        $this->assertFalse($this->storage->contains('ContentThatDoesntExist'));
    }

    public function testGetData()
    {
        $this->assertEquals($this->content, $this->storage->get($this->reference));
        $this->setExpectedException('Burger\Storage\Exception\StorageNotFoundException');
        $this->storage->get('notTheGoodReference');
    }

    public function testGetFirstData()
    {
        $this->assertEquals($this->content, $this->storage->getFirst());
        $this->storage->clearAll();
        $this->assertEmpty($this->storage->getFirst());
    }

    public function testGetAll()
    {
        $this->assertCount(1, $this->storage->getAll());
    }

    public function testDelete()
    {
        $this->assertTrue($this->storage->delete($this->reference));
        $this->assertFalse($this->storage->has($this->reference));
        $this->assertTrue($this->storage->delete($this->reference));
    }

    public function testClearAll()
    {
        $this->assertTrue($this->storage->clearAll());
    }

    protected function getStorage()
    {
        return new ProxyStorage('test');
    }
}
