<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductImageStorage
{
    public static function store(UploadedFile $file): string
    {
        $path = $file->store('products', 'public');

        return '/storage/'.$path;
    }

    /**
     * @param  list<UploadedFile|null>  $files
     * @return list<string>
     */
    public static function storeMany(array $files): array
    {
        return collect($files)
            ->filter(fn (?UploadedFile $file): bool => $file instanceof UploadedFile)
            ->map(fn (UploadedFile $file): string => self::store($file))
            ->values()
            ->all();
    }

    public static function deleteIfStored(?string $url): void
    {
        if ($url === null || $url === '' || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $relativePath = ltrim(str_replace('/storage/', '', $url), '/');

        if ($relativePath !== '' && Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
