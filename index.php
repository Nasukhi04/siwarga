<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Ambil flash message
$flash = get_flash();

// Query param
$q = trim($_GET['q'] ?? '');
$sort = strtolower($_GET['sort'] ?? 'asc');
$page = (int)($_GET['page'] ?? 1);

// Validasi sort (whitelist) agar aman
$sort = ($sort === 'desc') ? 'desc' : 'asc';
$orderBy = ($sort === 'desc') ? 'DESC' : 'ASC';

// Pagination
$perPage = 5;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

// WHERE dinamis untuk searching
$q = trim($_GET['q'] ?? '');
$sort = strtolower($_GET['sort'] ?? 'asc');
$page = (int)($_GET['page'] ?? 1);

$sort = ($sort === 'desc') ? 'desc' : 'asc';
$orderBy = ($sort === 'desc') ? 'DESC' : 'ASC';

$perPage = 5;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];

if ($q !== '') {
    $where = "WHERE nama LIKE :q1 OR email LIKE :q2 OR no_hp LIKE :q3";
    $keyword = "%{$q}%";
    $params[':q1'] = $keyword;
    $params[':q2'] = $keyword;
    $params[':q3'] = $keyword;
}

// 1) Hitung total data
$sqlCount = "SELECT COUNT(*) FROM users {$where}";
$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);
$totalRows = (int)$stmtCount->fetchColumn();

$totalPages = (int)ceil($totalRows / $perPage);
if ($totalPages < 1) $totalPages = 1;

// Jika page melebihi total page, sesuaikan
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

// 2) Ambil data list
$sql = "SELECT * FROM users
        {$where}
        ORDER BY nama {$orderBy}
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

if ($q !== '') {
    $stmt->bindValue(':q1', $keyword, PDO::PARAM_STR);
    $stmt->bindValue(':q2', $keyword, PDO::PARAM_STR);
    $stmt->bindValue(':q3', $keyword, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$rows = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">List Data User</h4>
    <a href="create.php" class="btn btn-primary">+ Tambah Data</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Form search + sorting -->
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Pencarian</label>
                <input type="text" name="q" class="form-control"
                       value="<?= e($q) ?>" placeholder="Cari nama / email / no HP">
            </div>

            <div class="col-md-3">
                <label class="form-label">Sorting Nama</label>
                <select name="sort" class="form-select">
                    <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>A-Z</option>
                    <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Z-A</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success w-100">Terapkan</button>
                <a href="index.php" class="btn btn-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr class="text-center">
                    <th width="50">No</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No HP</th>
                    <th>Tgl Lahir</th>
                    <th>JK</th>
                    <th>Pekerjaan</th>
                    <th>Agama</th>
                    <th>PDF</th>
                    <th>Created At</th>
                    <th width="170">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="12" class="text-center text-muted">Data tidak ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $i => $row): ?>
                        <tr>
                            <td class="text-center"><?= e((string)(($offset + $i + 1))) ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['foto'])): ?>
                                    <img src="<?= e($row['foto']) ?>" alt="Foto" class="thumb-img">
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($row['nama']) ?></td>
                            <td><?= e($row['email']) ?></td>
                            <td><?= e($row['no_hp']) ?></td>
                            <td><?= e($row['tanggal_lahir']) ?></td>
                            <td class="text-center"><?= e($row['jenis_kelamin']) ?></td>
                            <td><?= e($row['pekerjaan'] ?: '-') ?></td>
                            <td><?= e($row['agama'] ?? '-') ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['dokumen_pdf'])): ?>
                                    <a href="<?= e($row['dokumen_pdf']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        Lihat
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($row['created_at']) ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="edit.php?id=<?= e($row['id']) ?>" class="btn btn-sm btn-warning">Edit</a>

                                    <!-- Hapus pakai POST agar lebih aman -->
                                    <form action="delete.php" method="POST" class="d-inline form-delete">
                                        <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="11">
                                <strong>Alamat:</strong> <?= nl2br(e($row['alamat'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Info jumlah data -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
            <div class="text-muted small">
                Total data: <strong><?= e($totalRows) ?></strong> |
                Halaman <strong><?= e($page) ?></strong> dari <strong><?= e($totalPages) ?></strong>
            </div>

            <!-- Pagination sederhana -->
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php
                        $queryBase = [
                            'q' => $q,
                            'sort' => $sort
                        ];

                        $prevPage = $page - 1;
                        $nextPage = $page + 1;
                        ?>
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($queryBase, ['page' => $prevPage])) ?>">
                                Prev
                            </a>
                        </li>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($queryBase, ['page' => $p])) ?>">
                                    <?= e($p) ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($queryBase, ['page' => $nextPage])) ?>">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>