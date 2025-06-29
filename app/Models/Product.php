<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Kirschbaum\PowerJoins\PowerJoins;

class Product extends Model
{
    use HasFactory, SoftDeletes, Sluggable, PowerJoins;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($product)
        {
            if (Storage::exists($product->image))
                Storage::delete($product->image);

            $product->carts()->delete();
            $product->wishlists()->delete();
        });
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'method' => function ($string) {
                    $string = preg_replace('/[^a-z0-9ก-๙เแ]/i', '-', $string);
                    $string = preg_replace('/-+/', '-', $string);
                    return preg_replace('/-$|^-/', '', $string);
                },
            ],

        ];
    }
}
