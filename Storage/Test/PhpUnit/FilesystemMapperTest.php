<?php namespace Burger\Storage\Test\PhpUnit;

use Burger\IO\Filesystem\FilesystemFactory;
use Burger\Storage\Factory\Adapter\FilesystemMapper;

class FilesystemMapperTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->filesystem = $this->getFilesystem();
        $this->mapper = $this->getMapper($this->filesystem, 'test');
    }

    protected function tearDown()
    {
        $this->mapper->destroy();
        $this->mapper->clearAll();
    }

    public function testLoadNewFile()
    {
        $this->mapper->load();
        $this->assertEquals([], $this->mapper->load());
        $this->assertInternalType('array', $this->mapper->load());
        $this->assertCount(0, $this->mapper->load());
    }

    public function testSetData()
    {
        $this->mapper->load();
        $this->assertTrue($this->mapper->set('value', 'reference')->has('reference'));
        $this->assertTrue($this->mapper->contains('value'));
    }

    public function testSave()
    {
        $this->mapper->load();
        $this->assertTrue($this->mapper->save());
    }

    public function testUpdateData()
    {
        $this->mapper->load();
        $this->assertTrue($this->mapper->set('defaultvalue', 'reference')
            ->update('reference', 'value')->has('reference'));
        $this->assertTrue($this->mapper->contains('value'));
        $this->setExpectedException('\InvalidArgumentException');
        $this->mapper->update('badReference', 'value');
    }

    public function testHasReference()
    {
        $this->mapper->load();
        $this->assertFalse($this->mapper->has('badReference'));
        $this->assertTrue($this->mapper->set('data', 'reference')->has('reference'));
    }

    public function testDeleteData()
    {
        $this->mapper->load();
        $this->assertFalse($this->mapper->delete('badReference')->has('badReference'));
        $this->assertFalse($this->mapper->set('data', 'reference')->delete('reference')->has('reference'));
        $this->assertFalse($this->mapper->contains('data'));
    }

    public function testDestroyMapper()
    {
        $this->mapper->load();
        $this->assertTrue($this->mapper->destroy());
        $this->assertTrue($this->mapper->destroy());
    }

    public function testGetAll()
    {
        $this->mapper->load();
        $this->mapper->set('value', 'reference');
        $this->assertCount(1, $this->mapper->getAll());
        $this->assertInternalType('array', $this->mapper->getAll());
    }

    public function testClearAll()
    {
        $this->mapper->load();
        $this->assertCount(0, $this->mapper->clearAll()->getAll());
        $this->mapper->set('value', 'reference');
        $this->assertCount(0, $this->mapper->clearAll()->getAll());
    }

    public function testContains()
    {
        $this->mapper->load();
        $this->mapper->set('value', 'reference');
        $this->assertTrue($this->mapper->contains('value'));
        $this->assertFalse($this->mapper->contains('badValue'));
    }

    public function testCreateFile()
    {
        $this->mapper->destroy();
        $this->assertEquals([], $this->mapper->load());
        $this->assertInternalType('array', $this->mapper->load());
    }

    protected function getMapper($filesystem, $config)
    {
        return new FilesystemMapper($filesystem, $config);
    }

    protected function getFilesystem()
    {
        $factory = new FilesystemFactory();
        return $factory->create('test');
    }
}
