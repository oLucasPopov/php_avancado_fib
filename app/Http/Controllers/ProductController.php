<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use App\Services\CategoryService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class ProductController extends Controller {

  public function __construct(
    protected ProductService $productService,
    protected CategoryService $categoryService
  ){}

  public function index() {
    $products     = $this->productService->findPaginate(10);
    $categories   = $this->categoryService->findAll();
    $selected = [];
    return view('product.index', compact('products', 'categories', 'selected'));
  }

  public function add(){
    $categories = Category::get();
    return view('product.add', compact('categories'));
  }

  public function save(Request $request): RedirectResponse {

    $request->validate([
      'name' => 'required|min:1|max:255',
      'description'=>'sometimes',
    ]);

    $this->productService->create($request->all());

    return redirect()->route('product.index');
  }

  public function edit($id) {
    $product = $this->productService->find($id);
    $categories = $this->categoryService->findAll();
    $selected = [];
    $images = $product->images;
    if (!empty($product->categories)) {
        foreach ($product->categories as $category) {
            $selected[] = $category->pivot->category_id;
        }
        return view('product.edit', compact('product', 'categories', 'selected', 'images'));
    }

    return redirect()->route('product.index');
  }

  public function update(Request $request, $id): RedirectResponse {
    $request->validate([
      'name' => 'required|min:1|max:255',
      'description' => 'sometimes',
    ]);

    $this->productService->update($id, $request->all());

    return redirect()->route('product.index');
  }

  public function delete($id): RedirectResponse {
    $this->productService->delete($id);
    return redirect()->route('product.index');
  }

  public function search(Request $request) {
    $search = $request->input('name');
    $selected = $request->input('categories') ?: [];
    $products = $this->productService->search($search, $selected);
    $categories = $this->categoryService->findAll();

    return view('product.index', compact('products', 'categories', 'selected', 'search'));
  }
}
