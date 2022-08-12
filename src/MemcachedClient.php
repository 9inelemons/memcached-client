<?php

namespace Painlofi\MemcachedClient;

use Exception;
use Memcache;
use Painlofi\MemcachedClient\Exceptions\NotConnectedException;
use Painlofi\MemcachedClient\Exceptions\ServerAlreadyAddedException;
use Painlofi\MemcachedClient\Exceptions\ServerNotAddedException;

class MemcachedClient
{
    private Memcache $memcache;

    private array $serverPool = [];

    private static array $instances = [];

    protected function __construct()
    {
        $this->memcache = new Memcache();
    }

    /**
     * @throws ServerAlreadyAddedException
     */
    public static function addServer(string $host, int $port, string $alias): void
    {
        $client = static::getInstance();

        if (isset($client->serverPool[$alias])) {
            throw new ServerAlreadyAddedException();
        } else {
            $client->memcache->addServer($host);
            $client->serverPool[$alias] = [
                'port' => $port,
                'host' => $host
            ];
        }
    }

    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }

    /**
     * @throws ServerNotAddedException|NotConnectedException
     */
    public static function set(
        string $key,
        mixed $value,
        int $flag,
        int $expire,
        string $serverAlias
    ): bool {
        $client = static::getInstance();

        if (!isset($client->serverPool[$serverAlias])) {
            throw new ServerNotAddedException();
        } else {
            $server = $client->serverPool[$serverAlias];

            if ($client->memcache->connect($server['host'], $server['port'])) {
                return $client->memcache->set($key, $value, $flag, $expire);
            } else {
                throw new NotConnectedException();
            }
        }
    }

    /**
     * @throws ServerNotAddedException|NotConnectedException
     */
    public static function get(
        string $key,
        int $flag,
        string $serverAlias
    ): string|array|bool {
        $client = static::getInstance();

        if (!isset($client->serverPool[$serverAlias])) {
            throw new ServerNotAddedException();
        } else {
            $server = $client->serverPool[$serverAlias];

            if ($client->memcache->connect($server['host'], $server['port'])) {
                return $client->memcache->get($key, $value, $flag);
            } else {
                throw new NotConnectedException();
            }
        }
    }

    public static function getServerPool(): array
    {
        $client = static::getInstance();

        return $client->serverPool;
    }
}
