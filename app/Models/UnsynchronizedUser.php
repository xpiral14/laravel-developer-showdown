<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnsynchronizedUser extends Model
{

    protected $fillable = ['data'];
    protected function casts(): array
    {
        return [
            'data' => 'json'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function synchronize(): static
    {
        $this->update(['data' => []]);
        return $this;
    }
}
