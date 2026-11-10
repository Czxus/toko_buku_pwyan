<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Update dan Delete(nonaktifkan) tenant, melengkapi TenantAdminTest (Create/Read). */
class TenantCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, string> */
    private function payload(Tenant $tenant, array $override = []): array
    {
        return array_merge([
            'nama_toko' => $tenant->nama_toko,
            'domain_slug' => $tenant->domain_slug,
            'alamat_gudang' => $tenant->alamat_gudang,
            'kontak' => '081200000000',
        ], $override);
    }

    public function test_edit_form_is_prefilled(): void
    {
        $tenant = Tenant::factory()->create(['nama_toko' => 'Toko Lama']);
        $admin = User::factory()->adminPlatform()->create();

        $this->actingAs($admin)->get(route('admin.tenants.edit', $tenant))
            ->assertOk()
            ->assertSee('Toko Lama')
            ->assertSee($tenant->domain_slug);
    }

    public function test_admin_can_update_tenant_and_audit_records_before_after(): void
    {
        $tenant = Tenant::factory()->create(['nama_toko' => 'Toko Lama', 'pickup_aktif' => true]);
        $admin = User::factory()->adminPlatform()->create();

        $this->actingAs($admin)
            ->put(route('admin.tenants.update', $tenant), $this->payload($tenant, [
                'nama_toko' => 'Toko Baru',
                'bayar_di_tempat_aktif' => '1',
            ]))
            ->assertRedirect(route('admin.tenants.show', $tenant));

        $tenant->refresh();
        $this->assertSame('Toko Baru', $tenant->nama_toko);
        $this->assertFalse($tenant->pickup_aktif, 'checkbox tidak dikirim = nonaktif');
        $this->assertTrue($tenant->bayar_di_tempat_aktif);

        $log = AuditLog::where(['entity' => 'tenant', 'entity_id' => (string) $tenant->id, 'action' => 'updated'])->firstOrFail();
        $this->assertSame('Toko Lama', $log->before['nama_toko']);
        $this->assertSame('Toko Baru', $log->after['nama_toko']);
        $this->assertSame($admin->id, $log->actor_id);
    }

    public function test_update_without_changes_writes_no_audit(): void
    {
        $tenant = Tenant::factory()->create(['pickup_aktif' => false, 'bayar_di_tempat_aktif' => false, 'kontak' => '081200000000']);
        $admin = User::factory()->adminPlatform()->create();

        $this->actingAs($admin)->put(route('admin.tenants.update', $tenant), $this->payload($tenant))->assertRedirect();

        $this->assertFalse(AuditLog::where('action', 'updated')->exists());
    }

    public function test_slug_can_stay_the_same_but_not_collide_with_another_tenant(): void
    {
        $a = Tenant::factory()->create(['domain_slug' => 'toko-a']);
        $b = Tenant::factory()->create(['domain_slug' => 'toko-b']);
        $admin = User::factory()->adminPlatform()->create();

        $this->actingAs($admin)->put(route('admin.tenants.update', $a), $this->payload($a))
            ->assertSessionHasNoErrors();

        $this->actingAs($admin)->put(route('admin.tenants.update', $a), $this->payload($a, ['domain_slug' => 'toko-b']))
            ->assertSessionHasErrors('domain_slug');

        $this->assertSame('toko-a', $a->fresh()->domain_slug);
        $this->assertSame('toko-b', $b->fresh()->domain_slug);
    }

    public function test_non_admin_cannot_edit_update_or_change_status(): void
    {
        $tenant = Tenant::factory()->create(['nama_toko' => 'Toko Asli']);
        $seller = User::factory()->sellerAdmin($tenant)->create();

        $this->actingAs($seller)->get(route('admin.tenants.edit', $tenant))->assertForbidden();
        $this->actingAs($seller)->put(route('admin.tenants.update', $tenant), $this->payload($tenant, ['nama_toko' => 'Dibajak']))->assertForbidden();
        $this->actingAs($seller)->patch(route('admin.tenants.status', $tenant), ['status' => 'inactive'])->assertForbidden();

        $tenant->refresh();
        $this->assertSame('Toko Asli', $tenant->nama_toko);
        $this->assertSame('active', $tenant->status);
    }

    public function test_admin_can_deactivate_and_reactivate_tenant_with_audit(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->adminPlatform()->create();

        $this->actingAs($admin)->patch(route('admin.tenants.status', $tenant), ['status' => 'inactive'])->assertRedirect();
        $this->assertSame('inactive', $tenant->fresh()->status);

        $this->actingAs($admin)->patch(route('admin.tenants.status', $tenant), ['status' => 'active'])->assertRedirect();
        $this->assertSame('active', $tenant->fresh()->status);

        $this->assertSame(2, AuditLog::where(['entity' => 'tenant', 'action' => 'status_changed'])->count());
        $this->assertTrue(AuditLog::where('action', 'status_changed')->get()
            ->contains(fn ($l) => $l->before === ['status' => 'active'] && $l->after === ['status' => 'inactive']));
    }

    public function test_status_must_be_whitelisted(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->adminPlatform()->create();

        $this->actingAs($admin)->patch(route('admin.tenants.status', $tenant), ['status' => 'deleted'])
            ->assertSessionHasErrors('status');

        $this->assertSame('active', $tenant->fresh()->status);
    }

    public function test_tenant_cannot_be_hard_deleted(): void
    {
        $tenant = Tenant::factory()->create();
        $admin = User::factory()->adminPlatform()->create();

        $this->assertFalse(Route::has('admin.tenants.destroy'));
        $this->actingAs($admin)->delete('/admin/tenants/'.$tenant->id)->assertStatus(405);
        $this->assertTrue(Tenant::whereKey($tenant->id)->exists());
    }
}
