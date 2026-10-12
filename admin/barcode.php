<?php

require_once '../includes/barcode/php-barcode-generator-main/vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

$generator = new BarcodeGeneratorPNG();

header('Content-Type: image/png');

echo $generator->getBarcode(
    $_GET['order'],
    $generator::TYPE_CODE_128
);