<?php

namespace Tests\Unit;


use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testInvalidFileMove(): void
    {
        $mock = Mockery::mock(UploadedFile::class, function (MockInterface $mock ){
          $mock->shouldReceive('isValid')
           ->once()
           ->andReturnFalse();
        });

        $return = FileService::move($mock, '');
        
        $this->assertNull($return);
    }
}
