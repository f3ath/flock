<?php
namespace F3\Flock;

use PHPUnit_Framework_TestCase;

class LockRaceTest extends PHPUnit_Framework_TestCase
{
    private $file;

    protected function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'php');
    }

    protected function tearDown()
    {
        unset($this->file);
    }

    public function testBlocking()
    {
        $threads = 100;
        $iterations = 50;
        exec('php ' . __DIR__ . "/race.php {$this->file} $threads $iterations 1", $output);
        $prevAction = 'release';
        $prevPid = 0;
        foreach ($output as $line) {
            list($pid, $action) = preg_split('/ /', $line);
            if ('acquire' == $action) {
                $this->assertEquals('release', $prevAction);
            } else {
                $this->assertEquals('acquire', $prevAction);
                $this->assertEquals($prevPid, $pid);
            }
            $prevAction = $action;
            $prevPid = $pid;
        }
    }

    public function testNonBlocking()
    {
        $threads = 100;
        $iterations = 100;
        exec('php ' . __DIR__ . "/race.php {$this->file} $threads $iterations 0", $output);
        $prevAction = 'release';
        $prevPid = 0;
        foreach ($output as $line) {
            list($pid, $action) = preg_split('/ /', $line);
            if ('skip' == $action) {
                continue;
            }
            if ('acquire' == $action) {
                $this->assertEquals('release', $prevAction);
            } else {
                $this->assertEquals('acquire', $prevAction);
                $this->assertEquals($prevPid, $pid);
            }
            $prevAction = $action;
            $prevPid = $pid;
        }
    }
}
