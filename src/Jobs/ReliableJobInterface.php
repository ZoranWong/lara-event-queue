<?php


namespace ZoranWong\LaraEventQueue\Jobs;


interface ReliableJobInterface
{
    public function ack(): bool;

    public function nack(): bool;
}
