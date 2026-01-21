<?php

namespace App\Domain\History\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\History\Enums\HistoryStatusEnum;

class History extends Model
{
    protected $table = "history";
    protected $collection = 'history';
    protected $fillable = [
        "total",
        "status",
        "error",
        "started_at",
        "completed_at",
    ];

    protected $casts = [
        'starterd_at' => 'datetime',
        'completed_at' => 'datetime',
        'status' => HistoryStatusEnum::class,
    ];
}
