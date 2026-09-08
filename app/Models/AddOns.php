<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOns extends Model
{


    protected $fillable = ['name', 'price', 'duration_minutes', 'status'];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
    ];
}

