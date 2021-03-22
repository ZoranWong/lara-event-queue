<?php


namespace ZoranWong\LaraEventQueue\Jobs;


use Illuminate\Queue\Jobs\RedisJob;
use Illuminate\Support\Facades\Redis;

class ReliableRedisJob extends RedisJob implements ReliableJobInterface
{
    public $afterCommit = true;
    protected const QUEUE_COMMIT_PREFIX = 'reliable:redis:queue:commit:';

    protected function commitId()
    {
        return self::QUEUE_COMMIT_PREFIX.$this->getJobId();
    }

    public function ack(): bool
    {
        // TODO: Implement ack() method.
        return app('reliable.queue.cache')->set($this->commitId(), true);
    }

    public function nack(): bool
    {
        // TODO: Implement nack() method.
        return app('reliable.queue.cache')->set($this->commitId(), false);
    }

    public function getUUId(): string
    {
        // TODO: Implement getUUId() method.
        return $this->uuid();
    }

    public function lock(): bool
    {
        // TODO: Implement lock() method.
    }

    public function unlock()
    {
        // TODO: Implement unlock() method.
    }

    public function canRun(): bool
    {
        // TODO: Implement canRun() method.
    }
}
