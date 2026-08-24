<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'Workflow Platform'); ?> — Saint-Gobain Maroc</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full bg-brand-bg text-brand-navy antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12">

        
        <div class="mb-9 flex flex-col items-center gap-2.5">
            <img src="<?php echo e(asset('images/saint-gobain-logo.jpg')); ?>" alt="Saint-Gobain" class="h-11 w-auto">
            <p class="text-[13px] font-medium text-slate-500">Workflow Platform</p>
        </div>

        
        <div class="w-full <?php echo $__env->yieldContent('width', 'max-w-[400px]'); ?> rounded-xl border border-brand-border bg-white p-8 shadow-sm">
            <?php echo $__env->yieldContent('content'); ?>
        </div>

        <p class="mt-8 text-xs text-slate-400">© <?php echo e(date('Y')); ?> Saint-Gobain Maroc</p>
    </div>
</body>
</html>
<?php /**PATH C:\projects\to step 12 backup\resources\views/layouts/auth.blade.php ENDPATH**/ ?>