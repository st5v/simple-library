<?php

declare(strict_types=1);

namespace SimpleLibrary\States;

use Carbon\Carbon;

class BorrowedState implements State
{
    public function __construct(
        protected Carbon $borrowTime
    ) {
    }

    public function borrowTime(): Carbon
    {
        return $this->borrowTime;
    }
}
