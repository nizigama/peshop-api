<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JWT_Token extends Model
{
    use HasFactory;

    protected $table = "jwt_tokens";
    protected $fillable = ["unique_id", "user_id", "token_title", "restrictions", "permissions", "expires_at", "last_used_at", "refreshed_at"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
