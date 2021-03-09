<?php


namespace ZoranWong\LaraEventQueue;


use Illuminate\Support\Facades\Event;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob as BaseJob;

class EventJob extends BaseJob
{

    protected $eventName = null;
    protected $eventPayload = null;

    protected $queue = '';

    protected $connection = '';

    public function __construct(string $eventName, array $eventPayload)
    {
        $this->eventName = $eventName;
        $this->eventPayload = $eventPayload;
        $this->queue = config('lara_event_queue.queue');
        $this->connection = config('lara_event_queue.connection');
    }

    public function handle()
    {
        $data = $this->eventPayload;
        if($data['transaction_state'] === ''){

        }
        /**@var EventManager $eventManager */
        $eventManager = app('queue.event.manager');
        $eventClass = $eventManager->getEvent($this->eventName);
        if ($eventClass) {
            /**@var QueueEvent $event */
            $event = new $eventClass($this->eventPayload);
            event($event);
        } else {
            event($this->eventName, $this->eventPayload);
        }
    }
}
