<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('danger', 'Metode request tidak valid.');
    redirect('index.php');
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    set_flash('danger', 'ID tidak valid.');
    redirect('index.php');
}

try {
    // Ambil data file dulu
    $stmt = $pdo->prepare("SELECT foto, dokumen_pdf FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();

    if (!$user) {
        set_flash('danger', 'Data tidak ditemukan.');
        redirect('index.php');
    }

    // Hapus data di DB
    $stmtDelete = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmtDelete->execute([':id' => $id]);

    // Hapus file terkait dari folder upload
    if (!empty($user['foto'])) {
        delete_uploaded_file($user['foto']);
    }
    if (!empty($user['dokumen_pdf'])) {
        delete_uploaded_file($user['dokumen_pdf']);
    }

    set_flash('success', 'Data berhasil dihapus beserta file terkait.');
} catch (PDOException $e) {
    set_flash('danger', 'Gagal menghapus data: ' . $e->getMessage());
}

redirect('index.php');