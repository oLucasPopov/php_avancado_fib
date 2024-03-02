<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name', 'image'];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'products_categories');
    }
}
