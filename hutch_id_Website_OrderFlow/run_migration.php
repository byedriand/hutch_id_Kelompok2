<?php
putenv('APP_ENV=local');
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
exit($kernel->handle(
    new Symfony\Component\Console\Input\StringInput('migrate'),
    new Symfony\Component\Console\Output\BufferedOutput(),
));
