<?php

namespace App\Services;
use App\Repositories\ProductRepository;
use App\Enums\ImagePath;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ProductService {
  const IMAGE_PATH = 'images/category';

  public function __construct(protected ProductRepository $productRepository){}

  private function getDescription(): mixed{
    $client = app(
      Client::class, [
        'config'=> [
          'verify' => false,
          'http_errors'=> false,
          'allow_redirects' => true,
          'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Accept-Encoding' => 'gzip',
          ]
        ]
      ]
    );

    try {
      $response = $client->request(
        'GET', 'https://0f1d1f20265a4e5bbab0db3a63b996b0.api.mockbin.io/'
      );

      if ($response->getStatusCode() === 200) {
        $json = json_decode($response->getBody()->getContents(), true);
        return Arr::get($json, 'description');
      }

      throw new HttpClientException($response->getBody()->getContents());
    } catch(Throwable) {
      return null;
    }
  }

  public function findPaginate(int $qty){
    return $this->productRepository->findPaginate($qty);
  }

  public function create(array $data): Model {
    $images = Arr::get($data, 'images', []);
    $categories = Arr::get($data, 'categories');
    $name = Arr::get($data, 'name');
    $description = Arr::get($data, 'description');
    $descriptionAPI = $this->getDescription();

    if(!is_null($descriptionAPI)){
      $description = $descriptionAPI;
    }

    $product = $this->productRepository->create([
      'name'=>$name,
      'description'=>$description
    ]);

    $product->categories()->sync($categories);
    foreach ($images as $image) {
      $this->createProductImage($image, $product->id);
    }

    return $product;
  }

  private function createProductImage($image, int $id) {
    $fileName = FileService::move($image, ImagePath::PRODUCT->value);
    $this->productRepository->createImage([
      'product_id'=>$id,
      'image'=>$fileName
    ]);
  }

  public function find(int $id) {
    return $this->productRepository->find($id);
  }

  public function update(int $id, array $data): Model {
    $product = $this->productRepository->find($id);
    $images = Arr::get($data, 'images', []);
    $categories = Arr::get($data, 'categories');

    if(!empty($product)){
      foreach ($images as $image){
        $this ->createProductImage($image, $product->id);
      }
      $product->categories()->sync($categories);
      $product->update([
        'name'=>Arr::get($data, 'name'),
        'description' => Arr::get($data, 'description')
      ]);

      return $product;
    }
  }

  public function delete($id): ?bool {
    $product = $this->productRepository->find($id);

    if (!empty($product)) {
      if(!empty($product->images)){
        foreach($product->images as $image) {
          FileService::delete($image->image, ImagePath::PRODUCT->value);
        }
      }

      $product->categories()->detach();
      $product->images()->delete();
      return $product->delete();
    }

    return false;
  }

  public function search(?string $name, ?array $categories): Collection{
    return $this->productRepository->search($name, $categories);
  }
}
