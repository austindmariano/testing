<?php /* C:\xampp\crs\resources\views/app.blade.php */ ?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'COMTEQ Registration System')); ?></title>

    </head>
    <body>
        <div id="app">
            <example-component></example-component>
        </div>
        <script src="js/app.js"></script>
    </body>
</html>
