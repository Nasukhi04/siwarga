<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    set_flash('danger', 'ID tidak valid.');
    redirect('index.php');
}

// Ambil data lama
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    set_flash('danger', 'Data tidak ditemukan.');
    redirect('index.php');
}

$errors = [];
$formData = $user; // default form pakai data lama

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Validasi input utama
    [$validatedData, $errors] = validate_user_input($_POST);

    // Supaya form tetap menampilkan data input user saat error
    $formData = array_merge($user, $validatedData);

    // Path file akhir yang akan disimpan ke DB
    $newFotoPath = $user['foto'];
    $newPdfPath  = $user['dokumen_pdf'];

    // Penampung file baru sementara (jika upload file baru)
    $uploadedFotoBaru = null;
    $uploadedPdfBaru = null;

    // 2) Jika user upload foto baru -> validasi + simpan
    if (isset($_FILES['foto']) && ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $fotoError = null;
        $uploadedFotoBaru = upload_image($_FILES['foto'], $fotoError, false);
        if ($fotoError) {
            $errors['foto'] = $fotoError;
        } else {
            $newFotoPath = $uploadedFotoBaru;
            $formData['foto'] = $uploadedFotoBaru;
        }
    }

    // 3) Jika user upload PDF baru -> validasi + simpan
    if (isset($_FILES['dokumen_pdf']) && ($_FILES['dokumen_pdf']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $pdfError = null;
        $uploadedPdfBaru = upload_pdf($_FILES['dokumen_pdf'], $pdfError, false);
        if ($pdfError) {
            $errors['dokumen_pdf'] = $pdfError;
        } else {
            $newPdfPath = $uploadedPdfBaru;
            $formData['dokumen_pdf'] = $uploadedPdfBaru;
        }
    }

    // Jika ada error validasi setelah sempat upload file baru, hapus file baru tersebut
    if (!empty($errors)) {
        if ($uploadedFotoBaru) delete_uploaded_file($uploadedFotoBaru);
        if ($uploadedPdfBaru) delete_uploaded_file($uploadedPdfBaru);
    }

    // 4) Update DB jika valid
    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET
                        nama = :nama,
                        email = :email,
                        no_hp = :no_hp,
                        tanggal_lahir = :tanggal_lahir,
                        jenis_kelamin = :jenis_kelamin,
                        pekerjaan = :pekerjaan,
                        alamat = :alamat,
                        foto = :foto,
                        dokumen_pdf = :dokumen_pdf,
                        agama = :agama
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama' => $validatedData['nama'],
                ':email' => $validatedData['email'],
                ':no_hp' => $validatedData['no_hp'],
                ':tanggal_lahir' => $validatedData['tanggal_lahir'],
                ':jenis_kelamin' => $validatedData['jenis_kelamin'],
                ':pekerjaan' => $validatedData['pekerjaan'],
                ':alamat' => $validatedData['alamat'],
                ':foto' => $newFotoPath,
                ':dokumen_pdf' => $newPdfPath,
                ':agama' => $validatedData['agama'],
                ':id' => $id,
            ]);

            // 5) Jika update sukses dan ada file baru, hapus file lama
            if ($uploadedFotoBaru && !empty($user['foto']) && $user['foto'] !== $uploadedFotoBaru) {
                delete_uploaded_file($user['foto']);
            }
            if ($uploadedPdfBaru && !empty($user['dokumen_pdf']) && $user['dokumen_pdf'] !== $uploadedPdfBaru) {
                delete_uploaded_file($user['dokumen_pdf']);
            }

            set_flash('success', 'Data berhasil diupdate.');
            redirect('index.php');
        } catch (PDOException $e) {
            // Jika DB gagal, hapus file baru agar tidak yatim
            if ($uploadedFotoBaru) delete_uploaded_file($uploadedFotoBaru);
            if ($uploadedPdfBaru) delete_uploaded_file($uploadedPdfBaru);

            $errors['general'] = 'Gagal update data: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-warning">
        <h5 class="mb-0">Edit Data User</h5>
    </div>
    <div class="card-body">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <?php $isEdit = true; ?>
            <?php include __DIR__ . '/includes/form_fields.php'; ?>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">Update</button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>