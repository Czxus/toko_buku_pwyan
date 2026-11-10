<?php

namespace App\Services\Admin;

use App\Models\Tenant;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Aktif / nonaktif / suspend tenant (UC-13 "menonaktifkan"). Tidak ada hapus:
 * tenant yang punya buku dan pesanan harus tetap tersimpan sebagai data historis.
 */
class ChangeTenantStatus
{
    /** @var list<string> */
    public const ALLOWED = ['active', 'inactive', 'suspended'];

    public function __construct(private AuditLogger $audit) {}

    public function handle(Tenant $tenant, string $status): Tenant
    {
        if (! in_array($status, self::ALLOWED, true)) {
            throw new DomainException('Status tenant tidak dikenal.');
        }

        return DB::transaction(function () use ($tenant, $status): Tenant {
            $locked = Tenant::query()->lockForUpdate()->findOrFail($tenant->id);
            $before = $locked->status;

            if ($before === $status) {
                return $locked;
            }

            $locked->forceFill(['status' => $status])->save();

            $this->audit->record(
                'tenant', $locked->id, 'status_changed',
                ['status' => $before], ['status' => $status], $locked->id,
            );

            return $locked;
        });
    }
}
