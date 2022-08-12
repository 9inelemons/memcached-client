<?php

namespace Painlofi\MemcachedClient\Exceptions;

use Exception;

class ServerAlreadyAddedException extends Exception
{
    protected $message = 'Memcached server already added';
}
