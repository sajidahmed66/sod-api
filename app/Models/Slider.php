<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Slider extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::deleting(function($slider)
        {
            if (Storage::exists($slider->image))
                Storage::delete($slider->image);
        });
    }
}
