<?php


namespace ZoranWong\LaraEventQueue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use ZoranWong\LaraEventQueue\Jobs\OrderJobInterface;
use ZoranWong\LaraEventQueue\Jobs\ReliableJobInterface;

class EventJob implements ShouldQueue, ReliableJobInterface
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobAckTrait;

    protected $events = [];

    public $tries = 5;

    /**
     *
     * @param QueueEvent $event
     * @param array $others
     */
    public function __construct(QueueEvent $event, ...$others)
    {
        $this->queue = $event->getEventQueue();
        $this->connection = config('lara_event_queue.connection');
        $this->events[$event->getEventId()] = new QueueJobEvent($event->getPayload(), $event->getEventName(), $event->getTransactionId(), $event->getTransactionState());
        if (count($others) > 0) {
            foreach ($others as $otherEvent) {
                /**@var QueueEvent $otherEvent */
                $this->events[$otherEvent->getEventId()] = new QueueJobEvent($otherEvent->getPayload(), $otherEvent->getEventName(), $otherEvent->getTransactionId(), $otherEvent->getTransactionState());
            }
        }
    }

    /**
     * @throws
     * */
    public function handle()
    {
        foreach ($this->events as $k => $event) {
            try {
                $event->setJob($this);
                event($event);
                if($event->getPayload()['index'] %2 === 0)
                    Log::info('----------- event job ------', $event->getPayload());
                $event->setTransactionState(TransactionState::COMMIT);
                $this->ack();
                unset($this->events[$k]);
            } catch (\Exception $exception) {
                $event->setJob(null);
                $event->setTransactionState(TransactionState::ROLLBACK);
                if ($this->attempts() < $this->tries) {
                    if ($event->getFailToTail())
                        $this->release(1);
                    else
                        $this->nack();
                } else {
                    $this->job->fail($exception);
                }
                throw $exception;
            }
        }
    }

    public function getUUId(): string
    {
        // TODO: Implement getUUId() method.
        return $this->job->uuid();
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
        return true;
    }
}
