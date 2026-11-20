<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PhotoCompressionService
{
    public function store(UploadedFile $file, string $directory = 'finding-photos', string $disk = 'public'): string
    {
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false || !function_exists('imagecreatefromstring')) {
            return $file->store($directory, $disk);
        }

        $source = @imagecreatefromstring($contents);
        if (!$source) {
            throw new RuntimeException('Foto tidak dapat diproses.');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, 1920 / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $image = imagecreatetruecolor($targetWidth, $targetHeight);

        imagecopyresampled($image, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagejpeg($image, null, 80);
        $compressed = ob_get_clean();

        imagedestroy($source);
        imagedestroy($image);

        if ($compressed === false) {
            throw new RuntimeException('Foto tidak dapat dikompres.');
        }

        $path = $directory . '/' . Str::uuid() . '.jpg';
        Storage::disk($disk)->put($path, $compressed);

        return $path;
    }
}