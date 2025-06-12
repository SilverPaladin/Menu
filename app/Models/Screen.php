<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// added this line

class Screen extends Model
{
    protected $guarded = [];

    /**
     * Get the columns for the screen
     */
    public function columns()
    {
        return $this->hasMany(Column::class)->orderBy('id');
    }
}
