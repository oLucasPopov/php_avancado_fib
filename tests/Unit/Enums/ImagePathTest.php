<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\ImagePath;

class ImagePathTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testCategoryEnum(): void
    {
      $this->assertEquals('images/category', ImagePath::CATEGORY->value);
    }
    public function testProductproductEnum(): void
    {
      $this->assertEquals('images/category', ImagePath::CATEGORY->value);
    }
}
