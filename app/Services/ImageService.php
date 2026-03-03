<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    public static function saveBase64Image($base64)
    {
        if (!str_starts_with($base64, 'data:image')) {
            return null;
        }

        [$meta, $content] = explode(',', $base64);
        $extension = str_contains($meta, 'png') ? 'png' : 'jpg';

        $filename = 'news/' . Str::random(40) . '.' . $extension;

        Storage::disk('public')->put($filename, base64_decode($content));

        return $filename; // PATH LOKAL
    }
}
