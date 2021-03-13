<?php


namespace ZoranWong\LaraEventQueue;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class QueueEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    protected $transactionId = '';
    protected $transactionState = TransactionState::NOTHING;
    protected $payload = [];
    protected $eventId = '';
    protected $eventName = '';

    public function __construct(array $payload, string $eventName, string $transactionId = '', int $transactionState = TransactionState::NOTHING)
    {
        $this->payload = $payload;
        $this->transactionState = $transactionState;
        $this->transactionId = $transactionId;
        $this->eventName = $eventName;
        $this->eventId = md5(json_encode([$payload, $transactionId]));
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getTransactionState(): string
    {
        return $this->transactionState;
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

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getEventQueue(): string
    {
        return config('lara_event_queue.queue').$this->getEventName();
    }
}
