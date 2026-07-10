<?php

namespace App\Models;

use App\Enums\AttendanceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'stand_id', 'type', 'attended_at', 'notes'])]
class Attendance extends Model
{
    protected function casts(): array
    {
        return [
            'type' => AttendanceType::class,
            'attended_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }
}
