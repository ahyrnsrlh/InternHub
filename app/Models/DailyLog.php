<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DailyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'log_date',
        'department',
        'summary',
        'deliverable',
        'hours',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
            'hours' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(MentorReview::class);
    }
}
