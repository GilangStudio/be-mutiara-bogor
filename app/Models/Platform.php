<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $guarded = ['id'];

    // Relationships
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    // Accessors
    public function getLeadsCountAttribute()
    {
        return $this->leads()->count();
    }

    // Scopes
    public function scopeWithLeadsCount($query)
    {
        return $query->withCount('leads');
    }
}