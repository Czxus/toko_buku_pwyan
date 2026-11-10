<?php

namespace App\Services\Admin;

use App\Models\CommissionScheme;
use App\Models\Tenant;
use App\Models\TenantBalance;
use Illuminate\Support\Facades\DB;

/**
 * Onboarding tenant secara atomik: tenant + saldo awal nol + komisi aktif awal.
 * Jika salah satu langkah gagal (termasuk audit), semuanya dibatalkan (FR-ADM-01).
 */
class CreateTenant
{
    public function __construct(private AuditLogger $audit) {}

    /**
     * @param  array{
     *     nama_toko: string,
     *     domain_slug: string,
     *     alamat_gudang: string,
     *     kontak?: string|null,
     *     persen_komisi: int|float|string,
     *     pickup_aktif?: bool,
     *     bayar_di_tempat_aktif?: bool
     * }  $data  persen_komisi dalam persen (0-100), disimpan sebagai pecahan (0-1).
     */
    public function handle(array $data): Tenant
    {
        return DB::transaction(function () use ($data): Tenant {
            $tenant = (new Tenant)->forceFill([
                'nama_toko' => $data['nama_toko'],
                'domain_slug' => $data['domain_slug'],
                'alamat_gudang' => $data['alamat_gudang'],
                'kontak' => $data['kontak'] ?? null,
                'pickup_aktif' => $data['pickup_aktif'] ?? false,
                'bayar_di_tempat_aktif' => $data['bayar_di_tempat_aktif'] ?? false,
                'status' => 'active',
            ]);
            $tenant->save();

            (new TenantBalance)->forceFill([
                'tenant_id' => $tenant->id,
                'saldo_tersedia' => 0,
                'saldo_tertahan' => 0,
            ])->save();

            $komisi = (new CommissionScheme)->forceFill([
                'tenant_id' => $tenant->id,
                'persen_komisi' => round((float) $data['persen_komisi'] / 100, 4),
                'berlaku_mulai' => now(),
                'berlaku_sampai' => null,
            ]);
            $komisi->save();

            $this->audit->record(
                'tenant', $tenant->id, 'created',
                null,
                $tenant->only(['nama_toko', 'domain_slug', 'status']),
                $tenant->id,
            );
            $this->audit->record(
                'commission_scheme', $komisi->id, 'created',
                null,
                ['persen_komisi' => $komisi->persen_komisi, 'berlaku_mulai' => $komisi->berlaku_mulai],
                $tenant->id,
            );

            return $tenant;
        });
    }
}
