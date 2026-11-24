<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'seo_metadata' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(SiteSection::class)->orderBy('order', 'asc');
    }

    public function visibleSections(): HasMany
    {
        return $this->hasMany(SiteSection::class)->where('is_visible', true)->orderBy('order', 'asc');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/site/' . $this->slug);
    }
}
