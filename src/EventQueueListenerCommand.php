<?php


namespace ZoranWong\LaraEventQueue;


use Illuminate\Console\Command;

class EventQueueListenerCommand extends Command
{
    protected $signature = 'event-queue:listen';

    public function handle()
    {
        $this->call('queue:work', [
            'connection' => config('lara_event_queue.connection'),
            '--queue' => config('lara_event_queue.queue')
        ]);
    }
}
