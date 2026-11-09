<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInvitation extends Model
{
    protected $table = 'user_invitations';

    protected $fillable = [
        'user_id',
        'invited_by',
        'token_hash',
        'expires_at',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'accepted_by',
        'status',
        'terms_version',
        'privacy_version',
        'accepted_ip',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'sent_at' => 'datetime',
            'viewed_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by', 'id_usuario');
    }
}