Flock
=====

Simple locking mechanisms

#Usage

##FLock
```php
$file = '/tmp/my_lock.pid';
$lock = new F3\Lock\FLock($file);

// Non-blocking case. Acquire lock if it's free, otherwse exit immediately
if ($lock->acquire()) {
    // only one instance can reach here
    ...
    // do some job
    ...
    $lock->release();
} else {
    die('Another process is running')
}


// Waiting case. Acquire lock if it's free, otherwse block until it's free and then acquire
if ($lock->acquire(F3\Lock\FLock::BLOCKING)) {
    // only one instance can reach here
    ...
    // do some job
    ...
    $lock->release();
} else {
    // We sould not get to this point in this case
}

```
