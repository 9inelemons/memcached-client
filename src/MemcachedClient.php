<?php

namespace Painlofi\MemcachedClient;

use Memcache;
use Painlofi\MemcachedClient\Exceptions\NotConnectedException;
use Painlofi\MemcachedClient\Exceptions\ServerAlreadyAddedException;
use Painlofi\MemcachedClient\Exceptions\ServerNotAddedException;

/**
 * Class to control Memcache state
 */
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
     * Add server to Memcache server pool list
     *
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
     * Get value from Memcache server
     *
     * @param string $key
     * @param int $flag
     * @param string $serverAlias
     * @return string|array|bool
     * @throws NotConnectedException
     * @throws ServerNotAddedException
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
                return $client->memcache->get($key, $flag);
            } else {
                throw new NotConnectedException();
            }
        }
    }

    /**
     * Set value on Memcache server
     *
     * @param string $key
     * @param mixed $value
     * @param int $flag
     * @param int $expire
     * @param string $serverAlias
     * @return bool
     * @throws NotConnectedException
     * @throws ServerNotAddedException
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
     * Get server pool list
     *
     * @return array
     */
    public static function getServerPool(): array
    {
        $client = static::getInstance();

        return $client->serverPool;
    }
}
