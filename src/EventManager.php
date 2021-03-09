<?php


namespace ZoranWong\LaraEventQueue;


use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class EventManager
{
    protected $events = [];

    /**@var Application $app */
    protected $app = null;

    protected $transactionId = null;

    protected $transaction = false;

    protected $eventList = [];

    protected $transactionState = TransactionState::NOTHING;

    public function getEvent(string $name)
    {
        foreach ($this->events as $event) {
            if ((new \ReflectionClass($event))->getConstant('EVENT_NAME') === $name) {
                return $event;
            }
        }
        return null;
    }

    public function startTransaction()
    {
        $this->eventList = [];
        $this->transactionId = Str::random(32);
        $this->transaction = true;
        $this->transactionState = TransactionState::WAIT_COMMIT;
    }

    public function dispatch(QueueEvent $event)
    {
        if ($this->transaction) {
            $event->setTransactionId($this->transactionId)->setTransactionState($this->transactionState);
            if (!in_array($event, $this->eventList))
                $this->eventList[] = $event;
        } else {
            dispatch(new EventJob($event->getEventName(), $event->getPayload()));
        }
    }

    public function listener(string $event, $listener)
    {
        if (class_exists($event) && !in_array($event, $this->events)) {
            $this->events[] = $event;
        }
        Event::listen($event, $listener);
    }

    public function commitTransaction()
    {
        //
    }

    public function rollbackTransaction()
    {
        // 删除回滚
    }
}
