<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class Transaction extends Model
{
    use HasFactory, PowerJoins, SoftDeletes;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
