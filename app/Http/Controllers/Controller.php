<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

abstract class Controller
{
    protected function resolveSlug(string $name, ?string $slug): string
    {
        $resolved = Str::slug($slug !== null && $slug !== '' ? $slug : $name);

        return $resolved !== '' ? $resolved : Str::random(8);
    }
}
