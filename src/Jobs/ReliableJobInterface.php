<?php


namespace ZoranWong\LaraEventQueue\Jobs;


interface ReliableJobInterface
{
    public function ack(): bool;

    public function nack(): bool;

    public function getUUId(): string;

    public function lock(): bool;

    public function unlock();

    public function canRun(): bool;
}
