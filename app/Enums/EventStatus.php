<?php

namespace App\Enums;

enum EventStatus: int
{
    case Created = 0;
    case InProgress = 1;
    case Finished = 2;
}
