<?php
// includes/form_fields.php
// Variabel yang dipakai:
// $formData (array)
// $errors (array)
// $isEdit (bool)

$selectedPekerjaan = [];
if (!empty($formData['pekerjaan'])) {
    $selectedPekerjaan = array_map('trim', explode(',', $formData['pekerjaan']));
}
?>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama *</label>
        <input type="text" name="nama" class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
               value="<?= e($formData['nama'] ?? '') ?>" placeholder="Masukkan nama">
        <?php if (isset($errors['nama'])): ?>
            <div class="invalid-feedback"><?= e($errors['nama']) ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
               value="<?= e($formData['email'] ?? '') ?>" placeholder="contoh@email.com">
        <?php if (isset($errors['email'])): ?>
            <div class="invalid-feedback"><?= e($errors['email']) ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label">No HP *</label>
        <input type="text" name="no_hp" class="form-control <?= isset($errors['no_hp']) ? 'is-invalid' : '' ?>"
               value="<?= e($formData['no_hp'] ?? '') ?>" placeholder="08xxxxxxxxxx">
        <?php if (isset($errors['no_hp'])): ?>
            <div class="invalid-feedback"><?= e($errors['no_hp']) ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label">Tanggal Lahir *</label>
        <input type="date" name="tanggal_lahir" class="form-control <?= isset($errors['tanggal_lahir']) ? 'is-invalid' : '' ?>"
               value="<?= e($formData['tanggal_lahir'] ?? '') ?>">
        <?php if (isset($errors['tanggal_lahir'])): ?>
            <div class="invalid-feedback"><?= e($errors['tanggal_lahir']) ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label d-block">Jenis Kelamin *</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>" type="radio"
                   name="jenis_kelamin" id="jkL" value="L"
                   <?= (($formData['jenis_kelamin'] ?? '') === 'L') ? 'checked' : '' ?>>
            <label class="form-check-label" for="jkL">Laki-laki</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>" type="radio"
                   name="jenis_kelamin" id="jkP" value="P"
                   <?= (($formData['jenis_kelamin'] ?? '') === 'P') ? 'checked' : '' ?>>
            <label class="form-check-label" for="jkP">Perempuan</label>
        </div>
        <?php if (isset($errors['jenis_kelamin'])): ?>
            <div class="text-danger small mt-1"><?= e($errors['jenis_kelamin']) ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
    <label class="form-label d-block">Pekerjaan (checkbox)</label>
    <?php
    $pekerjaanOptions = ['Petani', 'Buruh', 'Wiraswasta', 'PNS', 'Lain-lain'];
    foreach ($pekerjaanOptions as $i => $pekerjaan):
    ?>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="pekerjaan[]"
                   id="pekerjaan<?= $i ?>" value="<?= e($pekerjaan) ?>"
                   <?= in_array($pekerjaan, $selectedPekerjaan, true) ? 'checked' : '' ?>>
            <label class="form-check-label" for="pekerjaan<?= $i ?>"><?= e($pekerjaan) ?></label>
        </div>
    <?php endforeach; ?>
</div>

    <div class="col-12">
        <label class="form-label">Alamat *</label>
        <textarea name="alamat" rows="3" class="form-control <?= isset($errors['alamat']) ? 'is-invalid' : '' ?>"
                  placeholder="Masukkan alamat"><?= e($formData['alamat'] ?? '') ?></textarea>
        <?php if (isset($errors['alamat'])): ?>
            <div class="invalid-feedback"><?= e($errors['alamat']) ?></div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label">Foto (JPG/JPEG/PNG, max 2MB) <?= $isEdit ? '' : '*' ?></label>
        <input type="file" name="foto" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
               class="form-control <?= isset($errors['foto']) ? 'is-invalid' : '' ?>">
        <?php if (isset($errors['foto'])): ?>
            <div class="invalid-feedback"><?= e($errors['foto']) ?></div>
        <?php endif; ?>

        <?php if ($isEdit && !empty($formData['foto'])): ?>
            <div class="mt-2">
                <small class="text-muted d-block">Foto saat ini:</small>
                <img src="<?= e($formData['foto']) ?>" alt="Foto" class="img-preview border rounded">
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <label class="form-label">Dokumen PDF (max 5MB) <?= $isEdit ? '' : '*' ?></label>
        <input type="file" name="dokumen_pdf" accept=".pdf,application/pdf"
               class="form-control <?= isset($errors['dokumen_pdf']) ? 'is-invalid' : '' ?>">
        <?php if (isset($errors['dokumen_pdf'])): ?>
            <div class="invalid-feedback"><?= e($errors['dokumen_pdf']) ?></div>
        <?php endif; ?>

        <?php if ($isEdit && !empty($formData['dokumen_pdf'])): ?>
            <div class="mt-2">
                <small class="text-muted d-block">PDF saat ini:</small>
                <a href="<?= e($formData['dokumen_pdf']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                    Lihat PDF
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
    <label class="form-label">Agama *</label>
    <select name="agama" class="form-select <?= isset($errors['agama']) ? 'is-invalid' : '' ?>">
        <option value="">-- Pilih Agama --</option>
        <?php foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $agama): ?>
            <option value="<?= e($agama) ?>" <?= (($formData['agama'] ?? '') === $agama) ? 'selected' : '' ?>>
                <?= e($agama) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (isset($errors['agama'])): ?>
        <div class="invalid-feedback"><?= e($errors['agama']) ?></div>
    <?php endif; ?>
</div>
</div>