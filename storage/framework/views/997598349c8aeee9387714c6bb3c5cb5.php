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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight"><?php echo e($tenant->nama_toko); ?></h2>
            <a href="<?php echo e(route('admin.tenants.index')); ?>" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                <div class="rounded-lg bg-green-100 px-4 py-2 text-sm text-green-800"><?php echo e(session('status')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="rounded-lg bg-red-100 px-4 py-2 text-sm text-red-800"><?php echo e($errors->first()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php $aktif = $tenant->status === 'active'; ?>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Data toko</h3>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo e(route('admin.tenants.edit', $tenant)); ?>"
                           class="inline-flex items-center px-3 py-1.5 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Ubah</a>
                        <form method="POST" action="<?php echo e(route('admin.tenants.status', $tenant)); ?>"
                              onsubmit="return confirm('<?php echo e($aktif ? 'Nonaktifkan' : 'Aktifkan'); ?> <?php echo e(e($tenant->nama_toko)); ?>?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <input type="hidden" name="status" value="<?php echo e($aktif ? 'inactive' : 'active'); ?>">
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md font-semibold text-xs text-white uppercase tracking-widest <?php echo e($aktif ? 'bg-red-600 hover:bg-red-500' : 'bg-green-600 hover:bg-green-500'); ?>">
                                <?php echo e($aktif ? 'Nonaktifkan' : 'Aktifkan'); ?>

                            </button>
                        </form>
                    </div>
                </div>
                <dl class="mt-3 grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Slug</dt><dd class="font-mono"><?php echo e($tenant->domain_slug); ?></dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd><?php echo e($tenant->status); ?></dd></div>
                    <div><dt class="text-gray-500">Kontak</dt><dd><?php echo e($tenant->kontak ?? '—'); ?></dd></div>
                    <div><dt class="text-gray-500">Alamat gudang</dt><dd><?php echo e($tenant->alamat_gudang); ?></dd></div>
                    <div><dt class="text-gray-500">Pickup</dt><dd><?php echo e($tenant->pickup_aktif ? 'Aktif' : 'Tidak aktif'); ?></dd></div>
                    <div><dt class="text-gray-500">Bayar di tempat</dt><dd><?php echo e($tenant->bayar_di_tempat_aktif ? 'Aktif' : 'Tidak aktif'); ?></dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900">Saldo</h3>
                <dl class="mt-3 grid grid-cols-2 gap-6 text-sm">
                    <div><dt class="text-gray-500">Saldo tersedia</dt><dd>Rp <?php echo e(number_format((int) ($tenant->balance?->saldo_tersedia ?? 0), 0, ',', '.')); ?></dd></div>
                    <div><dt class="text-gray-500">Saldo tertahan</dt><dd>Rp <?php echo e(number_format((int) ($tenant->balance?->saldo_tertahan ?? 0), 0, ',', '.')); ?></dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900">Riwayat skema komisi</h3>
                <ul class="mt-3 space-y-1 text-sm text-gray-700">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li>
                            <?php echo e(number_format((float) $c->persen_komisi * 100, 2, ',', '.')); ?>%
                            &mdash; berlaku <?php echo e($c->berlaku_mulai->format('d/m/Y H:i')); ?>

                            s/d <?php echo e($c->berlaku_sampai ? $c->berlaku_sampai->format('d/m/Y H:i') : 'sekarang'); ?>

                        </li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <li class="text-gray-500">Belum ada skema komisi khusus tenant ini.</li>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
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
<?php /**PATH C:\ServBay\www\servbay\toko_buku_pwyan\resources\views/admin/tenants/show.blade.php ENDPATH**/ ?>