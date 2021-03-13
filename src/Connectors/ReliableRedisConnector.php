<?php


namespace ZoranWong\LaraEventQueue\Connectors;


use Illuminate\Queue\Connectors\RedisConnector;
use ZoranWong\LaraEventQueue\Queue\ReliableRedisQueue;

class ReliableRedisConnector extends RedisConnector
{
    public function connect(array $config)
    {
        return new ReliableRedisQueue(
            $this->redis, $config['queue'],
            $config['connection'] ?? $this->connection,
            $config['retry_after'] ?? 60,
            $config['block_for'] ?? null,
            $config['after_commit'] ?? null
        );
    }
}
