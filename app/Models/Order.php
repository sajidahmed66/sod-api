<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class Order extends Model
{
    use HasFactory, SoftDeletes, PowerJoins;

    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($order)
        {
            $order->items()->delete();
            $order->transactions()->delete();
            $order->statusHistories()->delete();
        });
    }
}
