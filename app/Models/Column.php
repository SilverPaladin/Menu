<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Column extends Model
{
    protected $guarded = [];

    /**
     * Get the screen that owns the column
     */
    public function screen(): BelongsTo
    {
        return $this->belongsTo(Screen::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
