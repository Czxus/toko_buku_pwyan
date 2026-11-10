<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah Tenant: {{ $tenant->nama_toko }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nama_toko" value="Nama toko" />
                        <x-text-input id="nama_toko" name="nama_toko" type="text" class="mt-1 block w-full" :value="old('nama_toko', $tenant->nama_toko)" required autofocus />
                        <x-input-error :messages="$errors->get('nama_toko')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="domain_slug" value="Slug toko (unik)" />
                        <x-text-input id="domain_slug" name="domain_slug" type="text" class="mt-1 block w-full" :value="old('domain_slug', $tenant->domain_slug)" required />
                        <x-input-error :messages="$errors->get('domain_slug')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="alamat_gudang" value="Alamat gudang" />
                        <textarea id="alamat_gudang" name="alamat_gudang" rows="3" required
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat_gudang', $tenant->alamat_gudang) }}</textarea>
                        <x-input-error :messages="$errors->get('alamat_gudang')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="kontak" value="Kontak (opsional)" />
                        <x-text-input id="kontak" name="kontak" type="text" class="mt-1 block w-full" :value="old('kontak', $tenant->kontak)" />
                        <x-input-error :messages="$errors->get('kontak')" class="mt-2" />
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="pickup_aktif" value="1" @checked(old() ? old('pickup_aktif') : $tenant->pickup_aktif) class="rounded border-gray-300">
                            Layanan pickup di toko aktif
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="bayar_di_tempat_aktif" value="1" @checked(old() ? old('bayar_di_tempat_aktif') : $tenant->bayar_di_tempat_aktif) class="rounded border-gray-300">
                            Bayar di tempat aktif
                        </label>
                    </div>

                    <p class="text-xs text-gray-500">Komisi diubah lewat skema bertanggal (tahap berikutnya), bukan di form ini.</p>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Simpan perubahan</x-primary-button>
                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
