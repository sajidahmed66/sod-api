<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'vendor_id', 'product_id', 'type', 'qty', 'unit_price', 'amount', 'paid', 'vendor', 'note', 'order_id'];
}
