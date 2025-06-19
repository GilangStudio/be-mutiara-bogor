<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload dan kompres gambar
     */
    public static function uploadAndCompress(UploadedFile $file, string $directory, int $quality = 80, int $maxWidth = 1200): string
    {
        // Generate nama file unik
        $filename = self::generateUniqueFilename($file, $directory);
        
        // Path lengkap
        $fullPath = $directory . '/' . $filename;
        
        // Buat image instance dengan Intervention Image
        $image = Image::read($file);
        
        // Resize jika lebih besar dari maxWidth
        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }
        
        // Konversi ke WebP dengan kompresi
        $image->toWebp($quality);
        
        // Simpan ke storage
        Storage::disk('public')->put($fullPath, $image->encode());
        
        return $fullPath;
    }

    /**
     * Upload gambar multiple dengan ukuran berbeda
     */
    public static function uploadWithMultipleSizes(UploadedFile $file, string $directory, array $sizes = []): array
    {
        $results = [];
        
        foreach ($sizes as $sizeName => $config) {
            $quality = $config['quality'] ?? 80;
            $maxWidth = $config['width'] ?? 1200;
            
            $filename = self::generateUniqueFilename($file, $directory, $sizeName);
            $fullPath = $directory . '/' . $filename;
            
            $image = Image::read($file);
            
            if ($image->width() > $maxWidth) {
                $image->scale(width: $maxWidth);
            }
            
            $image->toWebp($quality);
            Storage::disk('public')->put($fullPath, $image->encode());
            
            $results[$sizeName] = $fullPath;
        }
        
        return $results;
    }

    /**
     * Generate nama file unik
     */
    private static function generateUniqueFilename(UploadedFile $file, string $directory, string $suffix = ''): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($originalName);
        $suffix = $suffix ? '-' . $suffix : '';
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        
        // return $slug . $suffix . '-' . $timestamp . '-' . $random . '.webp';
        return Str::random(12) . '-' . $random . '.webp';
    }

    /**
     * Hapus file dari storage
     */
    public static function deleteFile(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }

    /**
     * Hapus multiple files
     */
    public static function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            self::deleteFile($path);
        }
    }

    /**
     * Update gambar (hapus yang lama, upload yang baru)
     */
    public static function updateImage(?UploadedFile $newFile, ?string $oldPath, string $directory, int $quality = 80, int $maxWidth = 1200): ?string
    {
        // Jika tidak ada file baru, kembalikan path lama
        if (!$newFile) {
            return $oldPath;
        }

        // Hapus file lama
        self::deleteFile($oldPath);

        // Upload file baru
        return self::uploadAndCompress($newFile, $directory, $quality, $maxWidth);
    }
}