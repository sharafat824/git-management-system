<?php
// app/Models/Repository.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'github_id',
        'name',
        'full_name',
        'url',
        'description',
        'private',
        'default_branch',
        'owner_info',
        'is_active',
        'webhook_id',
    ];

    protected $casts = [
        'owner_info' => 'array',
        'private' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
