<?php
// app/Models/UserGithubToken.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGithubToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'github_username',
        'access_token',
        'token_scopes',
        'token_expires_at',
    ];

    protected $casts = [
        'token_scopes' => 'array',
        'token_expires_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
