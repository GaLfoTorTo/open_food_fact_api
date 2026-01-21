<?php

namespace App\Domain\History\Enums;

enum HistoryStatusEnum: string
{
    case RUNNUNG = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}