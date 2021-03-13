<?php


namespace ZoranWong\LaraEventQueue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EventJob implements ShouldQueue
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
}
