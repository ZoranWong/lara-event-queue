<?php


namespace ZoranWong\LaraEventQueue\Jobs;


use Illuminate\Queue\Jobs\RedisJob;

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
}
