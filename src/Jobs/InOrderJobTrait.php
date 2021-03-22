<?php


namespace ZoranWong\LaraEventQueue\Jobs;


use Illuminate\Support\Facades\Redis;
use Predis\ClientInterface;

trait InOrderJobTrait
{
    /**@var ClientInterface $redis */
    protected $redis = null;

    protected function getRedis()
    {
        if (!$this->redis)
            $this->redis = Redis::connection(config('lara_event_queue.redis'))->client();
        return $this->redis;
    }

    public function addUUIdInOrder($uuid)
    {
        $redis = $this->getRedis();
        $redis->rpush($this->orderKey(), $uuid);
    }

    public function nextUUId()
    {
        $redis = $this->getRedis();
        return $redis->lrange($this->orderKey(), 0, 0);
    }

    public function popUUId()
    {
        $redis = $this->getRedis();
        return $redis->lpop($this->orderKey());
    }
}
