<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Business extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'social_links' => 'array',
        'business_hours' => 'array',
        'attributes' => 'array',
        'has_unresolved_conflicts' => 'boolean',
    ];

    public function site(): HasOne
    {
        return $this->hasOne(Site::class);
    }

    public function mediaFiles(): HasMany
    {
        return $this->hasMany(MediaFile::class);
    }

    public function dataSources(): HasMany
    {
        return $this->hasMany(ExtractedDataSource::class);
    }

    public function conflicts(): HasMany
    {
        return $this->hasMany(DataConflict::class);
    }

    public function pendingConflicts(): HasMany
    {
        return $this->hasMany(DataConflict::class)->where('status', 'pending');
    }
}
