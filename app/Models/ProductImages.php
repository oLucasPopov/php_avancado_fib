<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model{

  protected $table = 'products_images';
  protected $fillable = ['product_id', 'image'];

  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}
