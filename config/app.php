<?php
// config/app.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

// Base path project
define('BASE_PATH', dirname(__DIR__));

// Folder upload (path server)
define('UPLOAD_IMAGE_DIR', BASE_PATH . '/uploads/images/');
define('UPLOAD_PDF_DIR', BASE_PATH . '/uploads/pdfs/');

// URL upload (untuk ditampilkan di browser)
define('UPLOAD_IMAGE_URL', 'uploads/images/');
define('UPLOAD_PDF_URL', 'uploads/pdfs/');

// Batas ukuran file
define('MAX_IMAGE_SIZE', 2 * 1024 * 1024); // 2 MB = 2097152 bytes
define('MAX_PDF_SIZE', 5 * 1024 * 1024);   // 5 MB = 5242880 bytes

// Buat folder upload otomatis jika belum ada
if (!is_dir(UPLOAD_IMAGE_DIR)) {
    mkdir(UPLOAD_IMAGE_DIR, 0777, true);
}
if (!is_dir(UPLOAD_PDF_DIR)) {
    mkdir(UPLOAD_PDF_DIR, 0777, true);
}