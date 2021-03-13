<?php


namespace ZoranWong\LaraEventQueue;


use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;

trait JobAckTrait
{
    public function ack()
    {
        /**@var RabbitMQJob $job */
        $job = $this->job;
        if (method_exists($job, 'getRabbitMQ')) {
            $job->getRabbitMQ()->ack($job);
        }
    }

    public function nack()
    {
        /**@var RabbitMQJob $job */
        $job = $this->job;
        if (method_exists($job, 'getRabbitMQ')) {
            $job->getRabbitMQ()->reject($job);
        }
    }
}
