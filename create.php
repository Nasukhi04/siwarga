<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$formData = [
    'nama' => '',
    'email' => '',
    'no_hp' => '',
    'tanggal_lahir' => '',
    'jenis_kelamin' => '',
    'pekerjaan' => '',
    'alamat' => '',
    'agama' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Validasi field teks/radio/checkbox/date/select
    [$formData, $errors] = validate_user_input($_POST);

    // 2) Validasi + upload file (wajib saat create)
    $fotoPath = null;
    $pdfPath = null;

    if (!isset($errors['foto'])) {
        $fotoError = null;
        $fotoPath = upload_image($_FILES['foto'] ?? null, $fotoError, true);
        if ($fotoError) {
            $errors['foto'] = $fotoError;
        }
    }

    if (!isset($errors['dokumen_pdf'])) {
        $pdfError = null;
        $pdfPath = upload_pdf($_FILES['dokumen_pdf'] ?? null, $pdfError, true);
        if ($pdfError) {
            $errors['dokumen_pdf'] = $pdfError;
        }
    }

    // 3) Simpan ke database jika tidak ada error
    if (empty($errors)) {
        try {
           $sql = "INSERT INTO users 
        (nama, email, no_hp, tanggal_lahir, jenis_kelamin, pekerjaan, alamat, foto, dokumen_pdf, agama)
        VALUES
        (:nama, :email, :no_hp, :tanggal_lahir, :jenis_kelamin, :pekerjaan, :alamat, :foto, :dokumen_pdf, :agama)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama' => $formData['nama'],
                ':email' => $formData['email'],
                ':no_hp' => $formData['no_hp'],
                ':tanggal_lahir' => $formData['tanggal_lahir'],
                ':jenis_kelamin' => $formData['jenis_kelamin'],
                ':pekerjaan' => $formData['pekerjaan'],
                ':alamat' => $formData['alamat'],
                ':foto' => $fotoPath,
                ':dokumen_pdf' => $pdfPath,
                ':agama' => $formData['agama'],
            ]);

            set_flash('success', 'Data berhasil ditambahkan.');
            redirect('index.php');
        } catch (PDOException $e) {
            // Jika gagal simpan DB, hapus file yang sudah terupload agar tidak yatim
            if ($fotoPath) delete_uploaded_file($fotoPath);
            if ($pdfPath) delete_uploaded_file($pdfPath);

            $errors['general'] = 'Gagal menyimpan data ke database: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Tambah Data User</h5>
    </div>
    <div class="card-body">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <?php $isEdit = false; ?>
            <?php include __DIR__ . '/includes/form_fields.php'; ?>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>