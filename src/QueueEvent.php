<?php


namespace ZoranWong\LaraEventQueue;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    const EVENT_NAME = 'queue.event';
    protected $transactionId = null;
    protected $transactionState = null;

    public function __construct()
    {
    }

    public function getPayload(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'transaction_state' => $this->transactionState
        ];
    }

    public function setTransactionState(int $state)
    {
        $this->transactionState = $state;
        return $this;
    }

    public function setTransactionId(string $id)
    {
        $this->transactionId = $id;
        return $this;
    }

    public function getEventName()
    {
        return self::EVENT_NAME;
    }
}
