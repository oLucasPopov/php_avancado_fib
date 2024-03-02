<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
  public function __construct(protected Category $categoryModel)
  {}

  public function findPaginate(int $qtd)
  {
    return $this->categoryModel::where('active', true)->paginate($qtd);
  }

  public function findAll(){
    return $this->categoryModel::Get();
  }

  public function find(int $id): ?Model
  {
    return $this->categoryModel::find($id);
  }

  public function search(?string $name):?Collection   {
    return $this
      ->categoryModel
      ->where('name', 'ilike', '%'.$name.'%')
      ->where('active', true)
      ->get();
  }

  public function create(array $data): Model {
    return $this->categoryModel::create($data);
  }

}
