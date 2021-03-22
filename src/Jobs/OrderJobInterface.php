<?php


namespace ZoranWong\LaraEventQueue\Jobs;


interface OrderJobInterface
{
    public function orderKey(): string;

    public function nextUUId();

    public function popUUId();

    public function addUUIdInOrder($uuid);
}
