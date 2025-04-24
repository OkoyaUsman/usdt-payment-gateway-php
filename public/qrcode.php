<?php
require_once '../libs/phpqrcode/phpqrcode.php';

$data = $_GET['data'] ?? '';
if (empty($data)) {
    die('No data provided');
}
// Generate QR code
QRcode::png($data, false, QR_ECLEVEL_L, 10);
?> 