<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Tenant</h2>
            <a href="<?php echo e(route('admin.tenants.create')); ?>"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Buat Tenant
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                <div class="mb-4 rounded-lg bg-green-100 px-4 py-2 text-sm text-green-800"><?php echo e(session('status')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="mb-4 rounded-lg bg-red-100 px-4 py-2 text-sm text-red-800"><?php echo e($errors->first()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $komisi = $tenant->commissionSchemes->first();
                                $warna = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'suspended' => 'bg-yellow-100 text-yellow-800',
                                    'inactive' => 'bg-gray-100 text-gray-600',
                                ][$tenant->status] ?? 'bg-gray-100 text-gray-600';
                                $aktif = $tenant->status === 'active';
                            ?>
                            <tr class="border-t border-gray-100">
                                <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($tenant->nama_toko); ?></td>
                                <td class="px-4 py-3 font-mono text-gray-600"><?php echo e($tenant->domain_slug); ?></td>
                                <td class="px-4 py-3">
                                    <?php echo e($komisi ? number_format((float) $komisi->persen_komisi * 100, 2, ',', '.').'%' : '—'); ?>

                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold <?php echo e($warna); ?>"><?php echo e($tenant->status); ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-4">
                                        <a class="text-indigo-600 hover:underline" href="<?php echo e(route('admin.tenants.show', $tenant)); ?>">Detail</a>
                                        <a class="text-indigo-600 hover:underline" href="<?php echo e(route('admin.tenants.edit', $tenant)); ?>">Ubah</a>
                                        <form method="POST" action="<?php echo e(route('admin.tenants.status', $tenant)); ?>"
                                              onsubmit="return confirm('<?php echo e($aktif ? 'Nonaktifkan' : 'Aktifkan'); ?> <?php echo e(e($tenant->nama_toko)); ?>?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="<?php echo e($aktif ? 'inactive' : 'active'); ?>">
                                            <button type="submit" class="<?php echo e($aktif ? 'text-red-600' : 'text-green-700'); ?> hover:underline">
                                                <?php echo e($aktif ? 'Nonaktifkan' : 'Aktifkan'); ?>

                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada tenant. Klik "+ Buat Tenant" untuk menambah toko pertama.
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 text-xs text-gray-500">Tenant nonaktif disembunyikan dari katalog publik; data historisnya tidak dihapus.</p>
            <div class="mt-4"><?php echo e($tenants->links()); ?></div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\ServBay\www\servbay\toko_buku_pwyan\resources\views/admin/tenants/index.blade.php ENDPATH**/ ?>