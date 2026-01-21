<?php

namespace App\Domain\History\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use App\Domain\History\Enums\HistoryStatusEnum;

class History extends Model
{
    protected $table = "history";
    protected $collection = 'history';
    private $fillable = [
        "total",
        "status",
        "error",
        "started_at",
        "completed_at",
    ];

    protected $casts = [
        'starterd_at' => 'timestamps',
        'completed_at' => 'timestamps',
        'status' => HistoryStatusEnum::class,
    ];
}
