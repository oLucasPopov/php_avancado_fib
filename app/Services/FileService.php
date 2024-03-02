<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileService {
  public static function move(UploadedFile $file, string $filePath):?string {
    if($file->isValid()) {
      $fileName = time() . '.' . $file->getClientOriginalExtension();
      if ($file->move($filePath, $fileName)) {
        return $fileName;
      }
    }

    return null;
  }
  public static function delete(string $file, string $filePath): void {
    $fileLocation = $filePath . '/' . $file;

    if(file_exists($fileLocation )) {
      unlink($fileLocation);
    }
  }
}
