<?php
// includes/functions.php

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Flash message (pesan sekali tampil)
function set_flash($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash()
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

// Validasi field utama (selain file)
function validate_user_input($post)
{
    $errors = [];
    $data = [];

    $data['nama'] = trim($post['nama'] ?? '');
    $data['email'] = trim($post['email'] ?? '');
    $data['no_hp'] = trim($post['no_hp'] ?? '');
    $data['tanggal_lahir'] = trim($post['tanggal_lahir'] ?? '');
    $data['jenis_kelamin'] = trim($post['jenis_kelamin'] ?? '');
    $data['alamat'] = trim($post['alamat'] ?? '');
    $data['agama'] = trim($post['agama'] ?? '');

    // Checkbox pekerjaan (array -> string)
    $pekerjaanArray = $post['pekerjaan'] ?? [];
if (!is_array($pekerjaanArray)) {
    $pekerjaanArray = [];
}

    // Pekerjaan yang diizinkan (whitelist)
$allowedPekerjaan = ['Petani', 'Buruh', 'Wiraswasta', 'PNS', 'Lain-lain'];
$pekerjaanFiltered = array_values(array_intersect($pekerjaanArray, $allowedPekerjaan));
$data['pekerjaan'] = implode(', ', $pekerjaanFiltered);

    // Validasi nama
    if ($data['nama'] === '') {
        $errors['nama'] = 'Nama wajib diisi.';
    } elseif (mb_strlen($data['nama']) < 3) {
        $errors['nama'] = 'Nama minimal 3 karakter.';
    }

    // Validasi email
    if ($data['email'] === '') {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }

    // Validasi no_hp (sederhana)
    if ($data['no_hp'] === '') {
        $errors['no_hp'] = 'No HP wajib diisi.';
    } elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $data['no_hp'])) {
        $errors['no_hp'] = 'No HP hanya boleh angka/spasi/+/- (8-20 karakter).';
    }

    // Validasi tanggal_lahir
    if ($data['tanggal_lahir'] === '') {
        $errors['tanggal_lahir'] = 'Tanggal lahir wajib diisi.';
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $data['tanggal_lahir']);
        if (!$d || $d->format('Y-m-d') !== $data['tanggal_lahir']) {
            $errors['tanggal_lahir'] = 'Format tanggal lahir tidak valid.';
        }
    }

    // Validasi radio jenis_kelamin
    if (!in_array($data['jenis_kelamin'], ['L', 'P'], true)) {
        $errors['jenis_kelamin'] = 'Jenis kelamin wajib dipilih.';
    }

    // Validasi alamat
    if ($data['alamat'] === '') {
        $errors['alamat'] = 'Alamat wajib diisi.';
    }

    // Validasi agama
   if (!in_array($data['agama'], ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'], true)) {
    $errors['agama'] = 'Agama tidak valid.';
}

    return [$data, $errors];
}

// Generate nama file unik
function generate_unique_filename($extension, $prefix = 'file')
{
    return $prefix . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
}

// Upload gambar (JPG/JPEG/PNG max 2MB)
function upload_image($file, &$errorMessage = null, $required = true)
{
    if (!isset($file) || !isset($file['error'])) {
        if ($required) $errorMessage = 'File gambar tidak ditemukan.';
        return null;
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) $errorMessage = 'Foto wajib diupload.';
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = 'Terjadi error saat upload gambar.';
        return null;
    }

    if ($file['size'] > MAX_IMAGE_SIZE) {
        $errorMessage = 'Ukuran gambar maksimal 2 MB.';
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowedExt, true)) {
        $errorMessage = 'Format gambar harus JPG, JPEG, atau PNG.';
        return null;
    }

    // Validasi MIME type pakai finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMime = ['image/jpeg', 'image/png'];
    if (!in_array($mime, $allowedMime, true)) {
        $errorMessage = 'File gambar tidak valid.';
        return null;
    }

    $newName = generate_unique_filename($ext, 'img');
    $destination = UPLOAD_IMAGE_DIR . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errorMessage = 'Gagal menyimpan file gambar.';
        return null;
    }

    // simpan relative path (untuk DB)
    return UPLOAD_IMAGE_URL . $newName;
}

// Upload PDF (max 5MB)
function upload_pdf($file, &$errorMessage = null, $required = true)
{
    if (!isset($file) || !isset($file['error'])) {
        if ($required) $errorMessage = 'File PDF tidak ditemukan.';
        return null;
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) $errorMessage = 'Dokumen PDF wajib diupload.';
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = 'Terjadi error saat upload PDF.';
        return null;
    }

    if ($file['size'] > MAX_PDF_SIZE) {
        $errorMessage = 'Ukuran PDF maksimal 5 MB.';
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        $errorMessage = 'Format dokumen harus PDF.';
        return null;
    }

    // Validasi MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMime = ['application/pdf', 'application/x-pdf'];
    if (!in_array($mime, $allowedMime, true)) {
        $errorMessage = 'File PDF tidak valid.';
        return null;
    }

    $newName = generate_unique_filename('pdf', 'pdf');
    $destination = UPLOAD_PDF_DIR . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errorMessage = 'Gagal menyimpan file PDF.';
        return null;
    }

    return UPLOAD_PDF_URL . $newName;
}

// Hapus file dari relative path (uploads/images/... atau uploads/pdfs/...)
function delete_uploaded_file($relativePath)
{
    if (!$relativePath) return;

    $fullPath = BASE_PATH . '/' . ltrim($relativePath, '/');
    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}