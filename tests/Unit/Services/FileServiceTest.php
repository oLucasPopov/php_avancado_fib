<?php

namespace Tests\Unit;


use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\File\File;

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

    public function testValidFileAndMove(): void {
        $mock = Mockery::mock(UploadedFile::class, function(MockInterface $mock) {
            $fileMock = Mockery::mock(File::class);
            $mock->shouldReceive('isValid')
             ->once()
             ->andReturnTrue();

            $mock->shouldReceive('getClientOriginalExtension')
             ->once()
             ->andReturn('extension');

            $mock->shouldReceive('move')
             ->once()
             ->andReturn($fileMock);
        });

        $filePath = FileService::move($mock, '');

        $this->assertStringEndsWith('extension', $filePath);
    }
}
