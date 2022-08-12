<?php

namespace Painlofi\MemcachedClient\Tests;

use Painlofi\MemcachedClient\Exceptions\ServerAlreadyAddedException;
use Painlofi\MemcachedClient\Exceptions\ServerNotAddedException;
use Painlofi\MemcachedClient\MemcachedClient;
use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    protected function setUp(): void
    {
        $pool = MemcachedClient::getServerPool();
        if (count($pool) !== 1) {
            $host = 'localhost';
            $port = 11211;
            $alias = 'default';
            MemcachedClient::addServer($host, $port, $alias);
        }
    }

    /**
     * @covers ServerNotAddedException::class
     */
    public function testSetToNotAddedServer()
    {
        $this->expectException(ServerNotAddedException::class);
        $key = 'key';
        $value = 123;
        MemcachedClient::set($key, $value, 0, 3600, 'my_server');
    }

    /**
     * @covers MemcachedClient::set()
     * @covers MemcachedClient::get()
     */
    public function testSetAndGet()
    {
        $key = 'key';
        $value = 123;
        MemcachedClient::set($key, $value, 0, 3600, 'default');
        $result = MemcachedClient::get($key, 0, 'default');
        $this->assertEquals($value, $result);
    }

    /**
     * @covers ServerAlreadyAddedException::class
     */
    public function testAddServer()
    {
        $this->expectException(ServerAlreadyAddedException::class);
        $host = 'localhost';
        $port = 11211;
        $alias = 'default';
        MemcachedClient::addServer($host, $port, $alias);
    }
}