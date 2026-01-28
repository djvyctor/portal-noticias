<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PromotionRequest extends Model
{
    // constantes para status das solicitacoes
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    // campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'message',
        'status',
        'reviewed_by',
        'rejection_reason',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // relacao, uma solicitacao pertence a um usuario (jornalista)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relacao, uma solicitacao foi revisada por um usuario (editor/admin)
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // scope para solicitacoes pendentes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // scope para solicitacoes aprovadas
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    // scope para solicitacoes rejeitadas
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}
