<?php

namespace App\Services;

use Illuminate\Support\Str;

class GeneratorService
{
    /**
     * Generate unique slug untuk model
     */
    public static function generateSlug($model, $title, $id = null)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (self::slugExists($model, $slug, $id)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check apakah slug sudah ada
     */
    private static function slugExists($model, $slug, $id = null)
    {
        $query = $model::where('slug', $slug);
        
        if ($id) {
            $query->where('id', '!=', $id);
        }

        return $query->exists();
    }

    /**
     * Generate order number untuk model
     */
    public static function generateOrder($model, $id = null)
    {
        $query = $model::query();
        
        if ($id) {
            $query->where('id', '!=', $id);
        }

        $maxOrder = $query->max('order');
        
        return $maxOrder ? $maxOrder + 1 : 1;
    }

    /**
     * Reorder items setelah delete
     */
    public static function reorderAfterDelete($model)
    {
        $items = $model::orderBy('order')->get();
        
        foreach ($items as $index => $item) {
            $item->update(['order' => $index + 1]);
        }
    }
}