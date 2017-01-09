[![Travis Build](https://travis-ci.org/f3ath/flock.svg?branch=master)](https://travis-ci.org/f3ath/simpleuber)

#Flock. Simple locking mechanism on top of flock()

##Usage

```php
$file = '/tmp/my_lock.pid';
$lock = new F3\Flock\Lock($file);

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
if ($lock->acquire(F3\Flock\Lock::BLOCKING)) {
    // only one instance can reach here
    ...
    // do some job
    ...
    $lock->release();
} else {
    // We sould not get to this point in this case
}

```
