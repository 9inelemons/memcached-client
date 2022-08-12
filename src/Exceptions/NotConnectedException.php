<?php

namespace Painlofi\MemcachedClient\Exceptions;

use Exception;

class NotConnectedException extends Exception
{
    protected $message = 'Not connected to memcached server';
}
