<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProductRepository {
  public function __construct(
    protected Product $productModel,
    protected ProductImages $productImagesModel
  ){}

  public function findPaginate(int $qtd) {
    return $this->productModel->where('active', true)->paginate($qtd);
  }

  public function create(array $data): Model {
    return $this->productModel::create($data);
  }
  public function createImage(array $data): Model {
    return $this->productImagesModel::create($data);
  }

  public function find(int $id): ?Model{
    return $this->productModel::with('images')->find($id);
  }

  public function search(?string $name, ?array $categories): Collection {
    $query = DB::table('products')
      ->select('products.id', 'products.name', 'products.description')
      ->where('products.active', '=', true)
      ->join('products_categories', 'products.id', '=', 'products_categories.product_id')
      ->join('categories', 'products_categories.category_id', '=', 'categories.id')
      ->groupBy('products.id', 'products.name', 'products.description');

    if(!empty($name)){

      $query->where('products.description', 'like', '%'.$name.'%');
    }
    
    if(!empty($categories)){
      $query->whereIn('categories.id', $categories);
    }
    return $query->get();
  }
}

