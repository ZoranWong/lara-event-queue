<?php


namespace ZoranWong\LaraEventQueue;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Process\Process;
use Workerman\Worker;

abstract class EventQueueListenerCommand extends Command
{
    protected $signature = 'event-queue:listen {cmd}';
    protected $description = '消息事件队列';
    protected $workers = [];

    protected const START = 'start';

    protected const RESTART = 'restart';

    protected const RELOAD = 'reload';

    protected const STATUS = 'status';

    protected const STOP = 'stop';

    protected $output = null;

    protected $logPath = '/event-message/';

    protected $parentInput = null;

    public function __construct()
    {
        parent::__construct();
        $disk = app('filesystem')->disk('logs');
        if (!$disk->exists($this->logPath))
            $disk->makeDirectory($this->logPath);
    }

    public function handle()
    {
        $this->parentInput = $this->input;
        foreach ($this->getQueues() as $v) {
            $queue = $v['queue'];
            $workerNum = $v['worker_num'];
            $worker = new Worker();
            $worker->count = $workerNum;
            $worker->name = $this->signature;
            $worker->onWorkerStart = function () use ($queue, $worker) {
                $this->input = $this->parentInput;
                $this->output = new StreamOutput(fopen(app()->storagePath(). '/logs'. $this->logPath . $queue .'-'.now()->format('Y-m-d'). '-' . $worker->id . '.log', 'w'));
                $this->call('queue:work', [
                    'connection' => config('lara_event_queue.connection'),
                    '--queue' => $queue
                ]);
            };

            $this->workers[$worker->id] = $worker;
        }
        Worker::runAll();
    }

    abstract public function getQueues(): iterable;
}
