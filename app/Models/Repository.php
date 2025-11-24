<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    protected $fillable = [
        'github_id',
        'name',
        'full_name',
        'url',
        'description',
        'private',
        'default_branch',
        'owner_info',
    ];

    protected $casts = [
        'private' => 'boolean',
        'owner_info' => 'array',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
