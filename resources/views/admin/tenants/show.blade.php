<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $tenant->nama_toko }}</h2>
            <a href="{{ route('admin.tenants.index') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg bg-green-100 px-4 py-2 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-lg bg-red-100 px-4 py-2 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            @php $aktif = $tenant->status === 'active'; @endphp

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Data toko</h3>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.tenants.edit', $tenant) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Ubah</a>
                        <form method="POST" action="{{ route('admin.tenants.status', $tenant) }}"
                              onsubmit="return confirm('{{ $aktif ? 'Nonaktifkan' : 'Aktifkan' }} {{ e($tenant->nama_toko) }}?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $aktif ? 'inactive' : 'active' }}">
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md font-semibold text-xs text-white uppercase tracking-widest {{ $aktif ? 'bg-red-600 hover:bg-red-500' : 'bg-green-600 hover:bg-green-500' }}">
                                {{ $aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
                <dl class="mt-3 grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Slug</dt><dd class="font-mono">{{ $tenant->domain_slug }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd>{{ $tenant->status }}</dd></div>
                    <div><dt class="text-gray-500">Kontak</dt><dd>{{ $tenant->kontak ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Alamat gudang</dt><dd>{{ $tenant->alamat_gudang }}</dd></div>
                    <div><dt class="text-gray-500">Pickup</dt><dd>{{ $tenant->pickup_aktif ? 'Aktif' : 'Tidak aktif' }}</dd></div>
                    <div><dt class="text-gray-500">Bayar di tempat</dt><dd>{{ $tenant->bayar_di_tempat_aktif ? 'Aktif' : 'Tidak aktif' }}</dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900">Saldo</h3>
                <dl class="mt-3 grid grid-cols-2 gap-6 text-sm">
                    <div><dt class="text-gray-500">Saldo tersedia</dt><dd>Rp {{ number_format((int) ($tenant->balance?->saldo_tersedia ?? 0), 0, ',', '.') }}</dd></div>
                    <div><dt class="text-gray-500">Saldo tertahan</dt><dd>Rp {{ number_format((int) ($tenant->balance?->saldo_tertahan ?? 0), 0, ',', '.') }}</dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900">Riwayat skema komisi</h3>
                <ul class="mt-3 space-y-1 text-sm text-gray-700">
                    @forelse ($commissions as $c)
                        <li>
                            {{ number_format((float) $c->persen_komisi * 100, 2, ',', '.') }}%
                            &mdash; berlaku {{ $c->berlaku_mulai->format('d/m/Y H:i') }}
                            s/d {{ $c->berlaku_sampai ? $c->berlaku_sampai->format('d/m/Y H:i') : 'sekarang' }}
                        </li>
                    @empty
                        <li class="text-gray-500">Belum ada skema komisi khusus tenant ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
