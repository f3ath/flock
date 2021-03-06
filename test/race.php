<?php
/**
 *  Race condition test
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use F3\Flock\Lock;
use F3\ForkRunner\ForkRunner;

if ($argc < 5) {
    echo <<<INFO
Test concurrent lock/unlock to reveal race conditions.

Usage: race.php <pidfile> <processCount> <iterationsCount> <wait>

<pidfile> - pid file name
<processCount> - # of simultanious processes (int)
<iterationsCount> - lock/unlock cicles per each process (int)
<block> - blocking (0 - non-blocking, 1 - blocking)

INFO;
    die(1);
}

$pidfile = $argv[1];
$processCount = (int) $argv[2];
$iterationsCount = (int) $argv[3];
$block = (bool) (int) $argv[4];

$payload = function ($file, $count, $block = false) {
    $pid = getmypid();
    try {
        for ($i = 0; $i < $count; $i++) {
            $lock = new Lock($file);
            if ($lock->acquire($block)) {
                echo "$pid acquire\n";
                usleep(1);
                if ($pid != $fileContents = @file_get_contents($file)) {
                    throw new Exception("Invalid file contents '{$fileContents}'");
                }
                echo "$pid release\n";
                $lock->release();
            } else {
                echo "$pid skip\n";
            }
            usleep(1);
        }
    } catch (Exception $e) {
        echo "ERROR: $pid {$e->getMessage()}\n";
    }
};

$runner = new ForkRunner();
$res = $runner->run($payload, array_fill(0, $processCount, [$pidfile, $iterationsCount, $block]));
