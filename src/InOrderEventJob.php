<?php


namespace ZoranWong\LaraEventQueue;


use ZoranWong\LaraEventQueue\Jobs\InOrderJobTrait;
use ZoranWong\LaraEventQueue\Jobs\OrderJobInterface;

class InOrderEventJob extends EventJob implements OrderJobInterface
{
    use InOrderJobTrait;
    public function orderKey(): string
    {
        // TODO: Implement orderKey() method.
        return md5($this->queue);
    }

    public function lock(): bool
    {
        // TODO: Implement lock() method.
        return $this->redis->set($this->orderKey(), $this->getUUId(), "nx", "ex", 10);
    }

    public function unlock()
    {
        // TODO: Implement unlock() method.
        $this->redis->del($this->orderKey());
    }

    public function canRun(): bool
    {
        // TODO: Implement canRun() method.
        return $this->nextUUId() === $this->getUUId();
    }
}
