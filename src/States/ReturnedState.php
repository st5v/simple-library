<?php

declare(strict_types=1);

namespace SimpleLibrary\States;

use Carbon\Carbon;

class ReturnedState implements State
{
    public function __construct(
        protected Carbon $borrowTime,
        protected Carbon $returnTime
    ) {
    }

    public function borrowTime(): Carbon
    {
        return $this->borrowTime;
    }

    public function returnTime(): Carbon
    {
        return $this->returnTime;
    }
}
