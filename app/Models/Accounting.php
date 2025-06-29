<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    use HasFactory;
    protected $fillable = ['vendor_id', 'product_id', 'date', 'title', 'type', 'qty', 'unit', 'unit_price', 'amount', 'note', 'inventory_id', 'order_id'];
}
