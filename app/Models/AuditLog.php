<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * Jejak audit append-only. Tidak boleh di-update atau dihapus;
 * koreksi dibuat sebagai event baru (SRS FR-ADM-02: perubahan harus tercatat).
 */
class AuditLog extends Model
{
    protected $fillable = [
        'actor_id', 'tenant_id', 'entity', 'entity_id',
        'action', 'before', 'after', 'metadata', 'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after' => 'array',
            'metadata' => 'array',
            'logged_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Audit log bersifat append-only dan tidak boleh diubah.'));
        static::deleting(fn () => throw new LogicException('Audit log bersifat append-only dan tidak boleh dihapus.'));
    }

    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
