<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Services\FileService;

class CategoryService {
  const IMAGE_PATH = 'images/category';

  public function __construct(protected CategoryRepository $categoryRepository) {}

  public function findPaginate(int $qty){
    return $this->categoryRepository->findPaginate($qty);
  }
  
  public function find($id) : ?Model{
    return $this->categoryRepository->find($id);
  }

  public function findAll() : ?Collection{
    return $this->categoryRepository->findAll();
  }

  public function create(array $data){
    $image = Arr::get($data, 'image', []);
    $fileName = FileService::move($image, self::IMAGE_PATH);

    if (!is_null($fileName)){
      return $this->categoryRepository->create([
        'name' => Arr::get($data, 'name'),
        'image' => $fileName
      ]);
    }
  }

  public function update(int $id, array $data):bool {
    $category = $this->categoryRepository->find($id);

    if(!empty($category)){
      $newName= Arr::get($data, 'name');
      $newImage= Arr::get($data, 'image');
      $deletedImage= Arr::get($data, 'deleted_image');
  
      $update = [
        'name' => $newName
      ];
  
      if (!empty($newImage)){
        $fileName = FileService::move($newImage, self::IMAGE_PATH);
        $update['image'] = $fileName;
        FileService::delete($deletedImage, IMAGE_PATH);
      }
      return $category->update($update);
    }
    return false;
  }

  public function delete(int $id) {
    $category = $this->categoryRepository->find($id);

    if (!empty($category)) {
      FileService::delete($category->image, self::IMAGE_PATH);
      $category->products()->detach();

      return $category->delete();
    }
  }

  public function search(?string $name) {
    return $this->categoryRepository->search($name);
  }
}
