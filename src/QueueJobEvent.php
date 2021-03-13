<?php


namespace ZoranWong\LaraEventQueue;

class QueueJobEvent extends QueueEvent
{
    private $failToTail = true;

    private $job = null;

    public function __construct(array $payload, string $eventName, string $transactionId = null, int $transactionState = TransactionState::NOTHING)
    {
        parent::__construct($payload, $eventName, $transactionId, $transactionState);
    }

    public function getFailToTail()
    {
        return $this->failToTail;
    }

    public function setFailToTail(bool $tail): QueueJobEvent
    {
        $this->failToTail = $tail;
        return $this;
    }

    /**
     *
     * @param EventJob|null $job
     * @return QueueJobEvent
     */
    public function setJob($job): QueueJobEvent
    {
        $this->job = $job;
        return $this;
    }

    public function getJob(): EventJob
    {
        return $this->job;
    }
}
