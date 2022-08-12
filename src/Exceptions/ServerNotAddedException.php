<?php

namespace Painlofi\MemcachedClient\Exceptions;

use Exception;

class ServerNotAddedException extends Exception
{
    protected $message = 'Memcached server not added';
}
