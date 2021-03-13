<?php


namespace ZoranWong\LaraEventQueue;

use Exception;
use Illuminate\Support\Str;

class EventManager
{
    protected $events = [];

    protected $transactionId = null;

    protected $transaction = false;
    /**@var QueueEvent[] $eventList*/
    protected $eventList = [];

    protected $transactionState = TransactionState::NOTHING;

    protected static $instance = null;

    /**@var EventJob $job */
    private $job = null;

    public static function getInstance(): EventManager
    {
        if (!self::$instance) {
            self::$instance = new EventManager();
        }
        return self::$instance;
    }

    protected function __construct()
    {
    }

    public function startTransaction()
    {
        $this->eventList = [];
        $this->transactionId = Str::random(32);
        $this->transaction = true;
        $this->transactionState = TransactionState::WAIT_COMMIT;
    }

    /**
     *
     * @param string $eventName
     * @param array $payload
     * @throws Exception
     */
    public function dispatch(string $eventName, array $payload)
    {
        $event = new QueueEvent($payload, $eventName);
        if ($this->transaction) {
            $event->setTransactionId($this->transactionId)->setTransactionState($this->transactionState);
            if (!in_array($event, $this->eventList))
                $this->eventList[] = $event;
        } else {
            $this->job = new EventJob($event);
            try {
                dispatch($this->job);
                $this->job->ack();
            } catch (Exception $exception) {
                $this->job->nack();
                throw $exception;
            }
        }
    }

    /**
     * @throws
     * */
    public function commitTransaction()
    {
        //
        $this->job = new EventJob(...$this->eventList);
        try {
            dispatch($this->job);
            $this->job->ack();
            foreach ($this->eventList as $event) {
                $event->setTransactionState(TransactionState::COMMIT);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function rollbackTransaction()
    {
        // 删除回滚
        $this->job->nack();
        foreach ($this->eventList as $event) {
            $event->setTransactionState(TransactionState::ROLLBACK);
        }
    }
}
