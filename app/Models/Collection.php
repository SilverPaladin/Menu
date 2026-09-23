<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;

class Collection extends Model
{
    protected $guarded = [];

    public function columns(): hasMany
    {
        return $this->hasMany(Column::class);
    }

    public function items(): hasMany
    {
        return $this->hasMany(Item::class);
    }
}
