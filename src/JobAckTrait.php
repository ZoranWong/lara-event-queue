<?php


namespace ZoranWong\LaraEventQueue;


use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;
use ZoranWong\LaraEventQueue\Jobs\OrderJobInterface;

trait JobAckTrait
{
    public function ack(): bool
    {
        /**@var RabbitMQJob $job */
        $job = $this->job;
        if (method_exists($job, 'getRabbitMQ')) {
            $job->getRabbitMQ()->ack($job);
        }
//        if ($this instanceof OrderJobInterface) {
//            $this->addUUIdInOrder();
//        }
        return true;
    }

    public function nack(): bool
    {
        /**@var RabbitMQJob $job */
        $job = $this->job;
        if (method_exists($job, 'getRabbitMQ')) {
            $job->getRabbitMQ()->reject($job);
        }
//        if ($this instanceof OrderJobInterface) {
//            $this->popUUId();
//            $this->addUUIdInOrder();
//        }
        return true;
    }
}
