<?php /* C:\xampp\api\resources\views/layouts/vue.blade.php */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name')); ?></title>
</head>
<body>
    <div id="app">
        <?php echo $__env->yieldContent("content"); ?>
    </div>
    <script src="/js/app.js"></script>
</body>
</html>
