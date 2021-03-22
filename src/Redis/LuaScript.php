<?php


namespace ZoranWong\LaraEventQueue\Redis;

use Illuminate\Queue\LuaScripts as Script;

class LuaScript extends Script
{
    /**
     * Get the Lua script for pushing jobs onto the queue.
     *
     * KEYS[1] - The queue to push the job onto, for example: queues:foo
     * KEYS[2] - The notification list for the queue we are pushing jobs onto, for example: queues:foo:notify
     * ARGV[1] - The job payload
     *
     * @return string
     */
    public static function lpush()
    {
        return <<<'LUA'
-- Push the job onto the queue...
redis.call('lpush', KEYS[1], ARGV[1])
-- Push a notification onto the "notify" queue...
redis.call('lpush', KEYS[2], 1)
LUA;
    }
}
