<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImages;

class Product extends Model {
  protected $table = 'products';
  protected $fillable = ['name', 'description'];

  public function categories()
  {
    return $this->belongsToMany(Category::class, 'products_categories');
  }

  public function images(){
    return $this->hasMany(ProductImages::class);
  }
}
