<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Tenant</h2>
            <a href="{{ route('admin.tenants.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Buat Tenant
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-100 px-4 py-2 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-100 px-4 py-2 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="w-full min-w-[44rem] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Nama toko</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="px-4 py-3">Komisi aktif</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tenants as $tenant)
                            @php
                                $komisi = $tenant->commissionSchemes->first();
                                $warna = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'suspended' => 'bg-yellow-100 text-yellow-800',
                                    'inactive' => 'bg-gray-100 text-gray-600',
                                ][$tenant->status] ?? 'bg-gray-100 text-gray-600';
                                $aktif = $tenant->status === 'active';
                            @endphp
                            <tr class="border-t border-gray-100">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $tenant->nama_toko }}</td>
                                <td class="px-4 py-3 font-mono text-gray-600">{{ $tenant->domain_slug }}</td>
                                <td class="px-4 py-3">
                                    {{ $komisi ? number_format((float) $komisi->persen_komisi * 100, 2, ',', '.').'%' : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $warna }}">{{ $tenant->status }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-4">
                                        <a class="text-indigo-600 hover:underline" href="{{ route('admin.tenants.show', $tenant) }}">Detail</a>
                                        <a class="text-indigo-600 hover:underline" href="{{ route('admin.tenants.edit', $tenant) }}">Ubah</a>
                                        <form method="POST" action="{{ route('admin.tenants.status', $tenant) }}"
                                              onsubmit="return confirm('{{ $aktif ? 'Nonaktifkan' : 'Aktifkan' }} {{ e($tenant->nama_toko) }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $aktif ? 'inactive' : 'active' }}">
                                            <button type="submit" class="{{ $aktif ? 'text-red-600' : 'text-green-700' }} hover:underline">
                                                {{ $aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada tenant. Klik "+ Buat Tenant" untuk menambah toko pertama.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="mt-3 text-xs text-gray-500">Tenant nonaktif disembunyikan dari katalog publik; data historisnya tidak dihapus.</p>
            <div class="mt-4">{{ $tenants->links() }}</div>
        </div>
    </div>
</x-app-layout>
