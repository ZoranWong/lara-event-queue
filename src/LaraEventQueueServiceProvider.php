<?php


namespace ZoranWong\LaraEventQueue;

use Illuminate\Queue\QueueServiceProvider;
use ZoranWong\LaraEventQueue\Connectors\ReliableRedisConnector;

class LaraEventQueueServiceProvider extends QueueServiceProvider
{
    /**
     * Register the connectors on the queue manager.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    public function registerConnectors($manager)
    {
        foreach (['Null', 'Sync', 'Database', 'Redis', 'Beanstalkd', 'Sqs', 'ReliableRedis'] as $connector) {
            $this->{"register{$connector}Connector"}($manager);
        }
    }
    /**
     * Register the Redis queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerReliableRedisConnector($manager)
    {
        $manager->addConnector('reliable-redis', function () {
            return new ReliableRedisConnector($this->app['redis']);
        });
    }
}
