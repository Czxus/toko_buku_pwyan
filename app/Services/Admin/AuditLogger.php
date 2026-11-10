<?php

namespace App\Services\Admin;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Menulis jejak audit untuk aksi administratif.
 * Field sensitif (nomor rekening, password) dibuang dari diff sebelum disimpan.
 */
class AuditLogger
{
    /** @var list<string> */
    private const SENSITIVE = [
        'nomor_rekening_cipher', 'nomor_rekening', 'account_number', 'password', 'password_hash',
    ];

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    public function record(
        string $entity,
        int|string|null $entityId,
        string $action,
        ?array $before = null,
        ?array $after = null,
        ?int $tenantId = null,
    ): AuditLog {
        return AuditLog::create([
            'actor_id' => Auth::id(),
            'tenant_id' => $tenantId,
            'entity' => $entity,
            'entity_id' => $entityId !== null ? (string) $entityId : null,
            'action' => $action,
            'before' => $before !== null ? $this->sanitize($before) : null,
            'after' => $after !== null ? $this->sanitize($after) : null,
            'metadata' => [
                'request_id' => request()->header('X-Request-Id') ?? (string) Str::uuid(),
                'ip' => request()->ip(),
            ],
            'logged_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function sanitize(array $data): array
    {
        return array_diff_key($data, array_flip(self::SENSITIVE));
    }
}
