<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageService
{
    /**
     * Store an uploaded product image and return its relative path.
     *
     * @param  UploadedFile  $file
     * @return string  e.g. 'products/550e8400-e29b-41d4-a716-446655440000.jpg'
     */
    public function store(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->putFileAs('products', $file, $filename);

        return 'products/' . $filename;
    }

    /**
     * Delete a product image from storage if the path exists.
     *
     * @param  string|null  $path  Relative path stored in DB, e.g. 'products/uuid.jpg'
     */
    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
