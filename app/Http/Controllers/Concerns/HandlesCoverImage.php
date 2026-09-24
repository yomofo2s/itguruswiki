<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesCoverImage
{
    protected function syncCover(Request $request, Model $model, string $folder): void
    {
        if ($request->boolean('remove_cover') || $request->hasFile('cover')) {
            if ($model->cover_path) {
                Storage::disk('public')->delete($model->cover_path);
            }
            $model->cover_path = null;
        }

        if ($request->hasFile('cover')) {
            $model->cover_path = $request->file('cover')->store($folder, 'public');
        }
    }

    protected function deleteCover(Model $model): void
    {
        if ($model->cover_path) {
            Storage::disk('public')->delete($model->cover_path);
        }
    }
}
