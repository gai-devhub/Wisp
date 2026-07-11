<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'title',
        'content',
        'details',
        'image_path',
        'link_url',
        'is_active',
        'display_location',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get a human-readable status.
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Active' : 'Paused';
    }

    /**
     * Scope a query to only include active ads.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
