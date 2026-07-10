<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'region', 'address', 'is_active', 'checkout_time'])]
class Stand extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
