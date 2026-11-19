<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores an uploaded photo as a resized WebP (or JPEG) using PHP's built-in GD.
 *
 * - Caps the longest side (default 1600px) so pages load fast and the disk lasts.
 * - Applies the camera's EXIF rotation, so phone photos aren't sideways.
 * - Re-encoding drops all metadata, including the GPS location phones embed.
 *
 * Falls back to storing the original file if GD can't read it.
 */
class ImageOptimizer
{
    public static function store(UploadedFile $file, string $directory, int $maxSide = 1600, int $quality = 80): string
    {
        try {
            $optimized = self::encode($file, $maxSide, $quality);
        } catch (\Throwable $e) {
            Log::warning('Image optimisation failed; storing original.', ['error' => $e->getMessage()]);
            $optimized = null;
        }

        if ($optimized === null) {
            return $file->store($directory, 'public');
        }

        [$bytes, $extension] = $optimized;
        $path = trim($directory, '/').'/'.Str::random(40).'.'.$extension;
        Storage::disk('public')->put($path, $bytes);

        return $path;
    }

    /** @return array{0: string, 1: string}|null [bytes, extension] */
    private static function encode(UploadedFile $file, int $maxSide, int $quality): ?array
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        // Decoding needs ~4 bytes per pixel. Skip giant images rather than exhaust PHP memory.
        $size = @getimagesize($file->getRealPath());
        if (! $size || $size[0] * $size[1] > 40_000_000) {
            return null;
        }

        $contents = file_get_contents($file->getRealPath());
        $image = $contents !== false ? @imagecreatefromstring($contents) : false;
        if (! $image) {
            return null;
        }

        $image = self::applyExifOrientation($image, $file);

        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, $maxSide / max($width, $height));

        if ($scale < 1) {
            $resized = imagescale($image, (int) round($width * $scale), (int) round($height * $scale), IMG_BICUBIC);
            if ($resized) {
                imagedestroy($image);
                $image = $resized;
            }
        }

        ob_start();
        if (function_exists('imagewebp')) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            imagewebp($image, null, $quality);
            $extension = 'webp';
        } else {
            imagejpeg($image, null, $quality);
            $extension = 'jpg';
        }
        $bytes = ob_get_clean();
        imagedestroy($image);

        return $bytes ? [$bytes, $extension] : null;
    }

    private static function applyExifOrientation(\GdImage $image, UploadedFile $file): \GdImage
    {
        if (! function_exists('exif_read_data') || ! in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($file->getRealPath())['Orientation'] ?? 1);
        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        if ($rotated) {
            imagedestroy($image);

            return $rotated;
        }

        return $image;
    }
}
