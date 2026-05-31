<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$produk = \App\Models\Produk::whereIn('nama', [
    'Tas gendong',
    'Tas punggung mini',
    'Tas kanvas custom',
    'Tas laptop',
    'Totebag wanita'
])->get();

// Mapping nama produk ke filename gambar
$fotoMap = [
    'Tas gendong' => 'produk/tas-gendong.jpg',
    'Tas punggung mini' => 'produk/tas-punggung-mini.jpg',
    'Tas kanvas custom' => 'produk/tas-kanvas-custom.jpg',
    'Tas laptop' => 'produk/tas-laptop.jpg',
    'Totebag wanita' => 'produk/totebag-wanita.jpg',
];

foreach($produk as $p) {
    $fotoPath = $fotoMap[$p->nama] ?? null;
    if($fotoPath) {
        $p->foto = $fotoPath;
        $p->save();
        echo "✓ {$p->nama} - foto path: {$fotoPath}\n";
    }
}

echo "\nSelesai! Sekarang upload gambar ke folder: storage/app/public/produk/\n";
echo "Dengan nama file:\n";
echo "- tas-gendong.jpg\n";
echo "- tas-punggung-mini.jpg\n";
echo "- tas-kanvas-custom.jpg\n";
echo "- tas-laptop.jpg\n";
echo "- totebag-wanita.jpg\n";
