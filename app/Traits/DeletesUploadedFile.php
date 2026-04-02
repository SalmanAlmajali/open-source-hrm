<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait DeletesUploadedFile
{
    public static function bootDeletesUploadedFile()
    {
        static::deleting(function ($model) {
            foreach ($model->uploadAttributes() as $attribute) {
                $value = $model->{$attribute};

                // Tangani baik string maupun array
                $paths = is_array($value) ? $value : [$value];

                foreach ($paths as $path) {
                    // Pastikan path valid string dan bukan kosong
                    if (is_string($path) && trim($path) !== '') {
                        $disk = $model->uploadDisk();
                        if (Storage::disk($disk)->exists($path)) {
                            Storage::disk($disk)->delete($path);
                        }
                    }
                }
            }
        });
    }

    /**
     * Override in model to define which attributes are file paths.
     */
    protected function uploadAttributes(): array
    {
        return []; // default
    }

    /**
     * Override if using custom disk.
     */
    protected function uploadDisk(): string
    {
        return 'public'; // default disk
    }
}
