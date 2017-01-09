<?php
namespace F3\Flock;

use PHPUnit_Framework_TestCase;

class LockTest extends PHPUnit_Framework_TestCase
{
    private $file;

    protected function setUp()
    {
        $this->file = sys_get_temp_dir().'/'.sha1(mt_rand());
    }

    protected function tearDown()
    {
        unset($this->file);
    }

    public function testNormalBehavior()
    {
        // There is no file in the beginning
        $this->assertFalse(file_exists($this->file));
        $lockA = new Lock($this->file);
        $lockB = new Lock($this->file);
        // A can acquire the lock
        $this->assertTrue($lockA->acquire());
        // My PID is written
        $this->assertEquals(getmypid(), file_get_contents($this->file));
        // B can not acquire the lock
        $this->assertFalse($lockB->acquire());
        $lockA->release();
        // The file must be empty upon release
        $this->assertEquals('', file_get_contents($this->file));
        // B can now acquire
        $this->assertTrue($lockB->acquire());
        // If the object gets destroyed...
        unset($lockB);
        // ... the file should become empty
        $this->assertEquals('', file_get_contents($this->file));
        // ... and the lock must be released
        $this->assertTrue($lockA->acquire());
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage failed to open stream
     */
    public function testFileCanNotBeOpened()
    {
        $lockA = new Lock(sys_get_temp_dir());
        $lockA->acquire();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Lock is already acqiured
     */
    public function testDoubleAcquire()
    {
        $lockA = new Lock($this->file);
        $this->assertTrue($lockA->acquire());
        $lockA->acquire();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Lock is not acquired
     */
    public function testReleaseWithNoAcquire()
    {
        $lockA = new Lock($this->file);
        $lockA->release();
    }
}
