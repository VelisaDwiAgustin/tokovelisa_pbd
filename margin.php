<?php
require_once 'koneksi.php';

if (isset($_POST['simpan'])) {
    $persen = $_POST['persen'];
    $status = $_POST['status'];
    $iduser = $_SESSION['iduser'];

    if (isset($_POST['idmargin_penjualan']) && $_POST['idmargin_penjualan'] != '') {
        $id = $_POST['idmargin_penjualan'];
        mysqli_query($conn, "CALL sp_ubah_margin('$id', '$persen', '$status', '$iduser')");
        header("Location: margin.php?msg=updated");
    } else {
        mysqli_query($conn, "CALL sp_tambah_margin('$persen', '$status', '$iduser')");
        header("Location: margin.php?msg=added");
    }
    exit();
}

if (isset($_POST['hapus'])) {
    $id = $_POST['idmargin_penjualan'];
    mysqli_query($conn, "CALL sp_hapus_margin('$id')");
    header("Location: margin.php?msg=deleted");
    exit();
}

include 'header.php';
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php 
            if($_GET['msg'] == 'added') echo 'Data margin berhasil ditambahkan!';
            elseif($_GET['msg'] == 'updated') echo 'Data margin berhasil diperbarui!';
            elseif($_GET['msg'] == 'deleted') echo 'Data margin berhasil dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold text-success"><i class="bi bi-percent me-2"></i> Kelola Margin Penjualan</h5>
        <button type="button" class="btn btn-green fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Margin
        </button>
    </div>
    <div class="card-body p-4">
        
        <div class="table-responsive">
            <table class="table table-hover table-custom align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Margin (%)</th>
                        <th>Status</th>
                        <th>Diupdate Oleh</th>
                        <th>Waktu Update</th>
                        <th class="text-center" style="width: 180px;">Aksi (CUD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = mysqli_query($conn, "SELECT margin_penjualan.*, user.username FROM margin_penjualan JOIN user ON margin_penjualan.iduser = user.iduser ORDER BY idmargin_penjualan ASC");
                    while ($row = mysqli_fetch_assoc($data)):
                    ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $row['idmargin_penjualan']; ?></td>
                        <td><span class="fs-6 fw-bold text-success"><?= $row['persen']; ?>%</span></td>
                        <td>
                            <?= $row['status']==1 
                                ? '<span class="badge badge-active"><i class="bi bi-check-circle me-1"></i>Aktif</span>' 
                                : '<span class="badge badge-inactive"><i class="bi bi-x-circle me-1"></i>Non-Aktif</span>'; ?>
                        </td>
                        <td><i class="bi bi-person me-1 text-muted"></i><?= htmlspecialchars($row['username']); ?></td>
                        <td class="small text-muted"><?= $row['updated_at']; ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-yellow me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['idmargin_penjualan']; ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['idmargin_penjualan']; ?>">
                                <i class="bi bi-trash3"></i> Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- MODAL EDIT MARGIN -->
                    <div class="modal fade" id="modalEdit<?= $row['idmargin_penjualan']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Form Edit Margin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="margin.php" method="POST">
                                    <div class="modal-body text-start">
                                        <input type="hidden" name="idmargin_penjualan" value="<?= $row['idmargin_penjualan']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Persentase Margin (%)</label>
                                            <input type="number" step="0.1" name="persen" class="form-control" value="<?= $row['persen']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status Margin</label>
                                            <select name="status" class="form-select">
                                                <option value="1" <?= ($row['status']==1)?'selected':''; ?>>1 (Aktif - Otomatis Nonaktifkan Margin Lain)</option>
                                                <option value="0" <?= ($row['status']==0)?'selected':''; ?>>0 (Non-Aktif)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="simpan" class="btn btn-warning fw-bold">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL HAPUS MARGIN -->
                    <div class="modal fade" id="modalHapus<?= $row['idmargin_penjualan']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="margin.php" method="POST">
                                    <input type="hidden" name="idmargin_penjualan" value="<?= $row['idmargin_penjualan']; ?>">
                                    <div class="modal-body text-center py-4">
                                        <p class="fs-5 mb-0">Apakah Anda yakin ingin menghapus margin <strong><?= $row['persen']; ?>%</strong>?</p>
                                    </div>
                                    <div class="modal-footer bg-light justify-content-center">
                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="hapus" class="btn btn-danger px-4">Ya, Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- MODAL TAMBAH MARGIN -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Form Tambah Margin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="margin.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Persentase Margin (%)</label>
                        <input type="number" step="0.1" name="persen" class="form-control" placeholder="Contoh: 10.5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Margin</label>
                        <select name="status" class="form-select">
                            <option value="1">1 (Aktif - Otomatis Nonaktifkan Margin Lain)</option>
                            <option value="0">0 (Non-Aktif)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-primary fw-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>