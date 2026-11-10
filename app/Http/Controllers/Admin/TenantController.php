<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTenantRequest;
use App\Http\Requests\Admin\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\Admin\ChangeTenantStatus;
use App\Services\Admin\CreateTenant;
use App\Services\Admin\UpdateTenant;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CRUD tenant (UC-13): index/show (Read), create/store (Create),
 * edit/update (Update), updateStatus (pengganti Delete: nonaktifkan, bukan hapus).
 * Controller tipis: cek policy, panggil service, kembalikan view/redirect.
 */
class TenantController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CreateTenant $createTenant,
        private UpdateTenant $updateTenant,
        private ChangeTenantStatus $changeTenantStatus,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Tenant::class);

        $tenants = Tenant::query()
            ->with(['commissionSchemes' => fn ($query) => $query
                ->where('berlaku_mulai', '<=', now())
                ->where(fn ($q) => $q->whereNull('berlaku_sampai')->orWhere('berlaku_sampai', '>', now())),
            ])
            ->orderBy('nama_toko')
            ->paginate(10);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        $this->authorize('create', Tenant::class);

        return view('admin.tenants.create');
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $tenant = $this->createTenant->handle([
            'nama_toko' => $data['nama_toko'],
            'domain_slug' => $data['domain_slug'],
            'alamat_gudang' => $data['alamat_gudang'],
            'kontak' => $data['kontak'] ?? null,
            'persen_komisi' => $data['persen_komisi'],
            'pickup_aktif' => $request->boolean('pickup_aktif'),
            'bayar_di_tempat_aktif' => $request->boolean('bayar_di_tempat_aktif'),
        ]);

        return redirect()
            ->route('admin.tenants.show', $tenant)
            ->with('status', "Tenant {$tenant->nama_toko} berhasil dibuat dengan komisi aktif dan saldo awal.");
    }

    public function show(Tenant $tenant): View
    {
        $this->authorize('view', $tenant);

        $tenant->load('balance');
        $commissions = $tenant->commissionSchemes()->orderByDesc('berlaku_mulai')->get();

        return view('admin.tenants.show', compact('tenant', 'commissions'));
    }

    public function edit(Tenant $tenant): View
    {
        $this->authorize('update', $tenant);

        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validated();

        $this->updateTenant->handle($tenant, [
            'nama_toko' => $data['nama_toko'],
            'domain_slug' => $data['domain_slug'],
            'alamat_gudang' => $data['alamat_gudang'],
            'kontak' => $data['kontak'] ?? null,
            'pickup_aktif' => $request->boolean('pickup_aktif'),
            'bayar_di_tempat_aktif' => $request->boolean('bayar_di_tempat_aktif'),
        ]);

        return redirect()
            ->route('admin.tenants.show', $tenant)
            ->with('status', 'Data tenant berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->authorize('update', $tenant);

        $data = $request->validate([
            'status' => ['required', Rule::in(ChangeTenantStatus::ALLOWED)],
        ]);

        $this->changeTenantStatus->handle($tenant, $data['status']);

        $pesan = match ($data['status']) {
            'active' => "Tenant {$tenant->nama_toko} diaktifkan.",
            'inactive' => "Tenant {$tenant->nama_toko} dinonaktifkan. Data historis tetap tersimpan.",
            default => "Tenant {$tenant->nama_toko} disuspend.",
        };

        return back()->with('status', $pesan);
    }
}
