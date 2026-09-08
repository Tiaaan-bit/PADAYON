<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $fillable = ['name', 'description', 'duration_minutes', 'price', 'status'];
}
