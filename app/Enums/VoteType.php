<?php

namespace App\Enums;

enum VoteType: string
{
    case Up = 'up';
    case Down = 'down';

    public function score(): int
    {
        return $this === self::Up ? 1 : -1;
    }
}
