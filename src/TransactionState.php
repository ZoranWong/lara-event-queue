<?php


namespace ZoranWong\LaraEventQueue;


class TransactionState
{
    const NOTHING = 0;
    const WAIT_COMMIT = 1;
    const COMMIT = 2;
    const ROLLBACK = 3;
    const DONE = 4;
}
