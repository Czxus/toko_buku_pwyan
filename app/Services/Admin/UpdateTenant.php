<?php

namespace App\Services\Admin;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

/**
 * Ubah data tenant (UC-13 "ubah"). Komisi dan status TIDAK diubah di sini:
 * komisi lewat skema bertanggal (Tahap 4), status lewat ChangeTenantStatus.
 */
class UpdateTenant
{
    private const FIELDS = [
        'nama_toko', 'domain_slug', 'alamat_gudang', 'kontak', 'pickup_aktif', 'bayar_di_tempat_aktif',
    ];

    public function __construct(private AuditLogger $audit) {}

    /**
     * @param  array{
     *     nama_toko: string,
     *     domain_slug: string,
     *     alamat_gudang: string,
     *     kontak?: string|null,
     *     pickup_aktif?: bool,
     *     bayar_di_tempat_aktif?: bool
     * }  $data
     */
    public function handle(Tenant $tenant, array $data): Tenant
    {
        return DB::transaction(function () use ($tenant, $data): Tenant {
            $locked = Tenant::query()->lockForUpdate()->findOrFail($tenant->id);
            $before = $locked->only(self::FIELDS);

            $locked->forceFill([
                'nama_toko' => $data['nama_toko'],
                'domain_slug' => $data['domain_slug'],
                'alamat_gudang' => $data['alamat_gudang'],
                'kontak' => $data['kontak'] ?? null,
                'pickup_aktif' => $data['pickup_aktif'] ?? false,
                'bayar_di_tempat_aktif' => $data['bayar_di_tempat_aktif'] ?? false,
            ])->save();

            if ($locked->wasChanged()) {
                $this->audit->record(
                    'tenant', $locked->id, 'updated',
                    $before, $locked->only(self::FIELDS), $locked->id,
                );
            }

            return $locked;
        });
    }
}
