<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\CommissionScheme;
use App\Models\Tenant;
use App\Models\TenantBalance;
use App\Models\User;
use App\Services\Admin\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use RuntimeException;
use Tests\TestCase;

class TenantAdminTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, string> */
    private function payload(array $override = []): array
    {
        return array_merge([
            'nama_toko' => 'Toko Buku Mawar',
            'domain_slug' => 'toko-mawar',
            'alamat_gudang' => 'Jl. Mawar No. 1, Banyuwangi',
            'kontak' => '081234567890',
            'persen_komisi' => '12.5',
        ], $override);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.tenants.index'))->assertRedirect(route('login'));
    }

    public function test_only_admin_platform_can_open_admin_pages(): void
    {
        $seller = User::factory()->sellerAdmin()->create();
        $this->actingAs($seller)->get(route('admin.tenants.index'))->assertForbidden();
        $this->actingAs($seller)->get(route('admin.tenants.create'))->assertForbidden();
        $this->actingAs($seller)->post(route('admin.tenants.store'), $this->payload())->assertForbidden();

        $pembeli = User::factory()->create();
        $this->actingAs($pembeli)->get(route('admin.tenants.index'))->assertForbidden();

        $this->assertFalse(Tenant::where('domain_slug', 'toko-mawar')->exists());
    }

    public function test_admin_platform_can_list_tenants(): void
    {
        Tenant::factory()->create(['nama_toko' => 'Toko Alpha']);
        Tenant::factory()->create(['nama_toko' => 'Toko Beta']);
        $admin = User::factory()->adminPlatform()->create();

        $this->actingAs($admin)->get(route('admin.tenants.index'))
            ->assertOk()
            ->assertSee('Toko Alpha')
            ->assertSee('Toko Beta');
    }

    public function test_creating_tenant_also_creates_balance_active_commission_and_audit(): void
    {
        $admin = User::factory()->adminPlatform()->create();

        $this->actingAs($admin)
            ->post(route('admin.tenants.store'), $this->payload())
            ->assertRedirect();

        $tenant = Tenant::where('domain_slug', 'toko-mawar')->firstOrFail();
        $this->assertSame('active', $tenant->status);
        $this->assertTrue(TenantBalance::whereKey($tenant->id)->exists());

        $aktif = CommissionScheme::where('tenant_id', $tenant->id)->whereNull('berlaku_sampai')->get();
        $this->assertCount(1, $aktif);
        $this->assertSame('0.1250', $aktif->first()->persen_komisi);

        $this->assertTrue(AuditLog::where([
            'entity' => 'tenant',
            'entity_id' => (string) $tenant->id,
            'action' => 'created',
            'actor_id' => $admin->id,
        ])->exists());

        $this->get(route('admin.tenants.show', $tenant))
            ->assertOk()
            ->assertSee('Toko Buku Mawar')
            ->assertSee('12,50%');
    }

    public function test_slug_must_be_unique(): void
    {
        Tenant::factory()->create(['domain_slug' => 'toko-mawar']);
        $admin = User::factory()->adminPlatform()->create();
        $jumlahAwal = Tenant::count();

        $this->actingAs($admin)
            ->post(route('admin.tenants.store'), $this->payload())
            ->assertSessionHasErrors('domain_slug');

        $this->assertSame($jumlahAwal, Tenant::count());
    }

    public function test_commission_must_be_between_0_and_100(): void
    {
        $admin = User::factory()->adminPlatform()->create();

        foreach (['-1', '100.01', '101', 'abc'] as $nilai) {
            $this->actingAs($admin)
                ->post(route('admin.tenants.store'), $this->payload(['persen_komisi' => $nilai]))
                ->assertSessionHasErrors('persen_komisi');
        }

        $this->assertFalse(Tenant::where('domain_slug', 'toko-mawar')->exists());
    }

    public function test_onboarding_is_atomic_when_a_step_fails(): void
    {
        $admin = User::factory()->adminPlatform()->create();

        $this->mock(AuditLogger::class, function ($mock): void {
            $mock->shouldReceive('record')->andThrow(new RuntimeException('audit gagal'));
        });
        $this->withoutExceptionHandling();

        try {
            $this->actingAs($admin)->post(route('admin.tenants.store'), $this->payload());
            $this->fail('Seharusnya melempar RuntimeException.');
        } catch (RuntimeException) {
            // diharapkan
        }

        $this->assertFalse(Tenant::where('domain_slug', 'toko-mawar')->exists(), 'tenant harus ikut dibatalkan');
        $this->assertSame(0, CommissionScheme::whereNotNull('tenant_id')->count(), 'komisi harus ikut dibatalkan');
    }

    public function test_audit_logger_strips_sensitive_fields(): void
    {
        $this->actingAs(User::factory()->adminPlatform()->create());

        app(AuditLogger::class)->record('tenant_bank_account', 1, 'created', null, [
            'bank_code' => 'BCA',
            'rekening_last4' => '7890',
            'nomor_rekening_cipher' => '1234567890',
            'password' => 'rahasia',
        ]);

        $log = AuditLog::latest('id')->firstOrFail();
        $this->assertSame(['bank_code' => 'BCA', 'rekening_last4' => '7890'], $log->after);
        $this->assertStringNotContainsString('1234567890', json_encode($log->getAttributes()));
        $this->assertStringNotContainsString('rahasia', json_encode($log->getAttributes()));
    }

    public function test_audit_log_is_append_only(): void
    {
        $this->actingAs(User::factory()->adminPlatform()->create());
        $log = app(AuditLogger::class)->record('tenant', 1, 'created');

        $this->expectException(LogicException::class);
        $log->update(['action' => 'diubah']);
    }
}
