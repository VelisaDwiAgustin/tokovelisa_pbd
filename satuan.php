<?php
require_once 'koneksi.php';

// PROSES TAMBAH & EDIT MEMANGGIL STORED PROCEDURE
if (isset($_POST['simpan'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_satuan']);
    $status = $_POST['status'];

    if (isset($_POST['idsatuan']) && $_POST['idsatuan'] != '') {
        $id = $_POST['idsatuan'];
        mysqli_query($conn, "CALL sp_ubah_satuan('$id', '$nama', '$status')");
        header("Location: satuan.php?msg=updated");
    } else {
        mysqli_query($conn, "CALL sp_tambah_satuan('$nama', '$status')");
        header("Location: satuan.php?msg=added");
    }
    exit();
}

// PROSES HAPUS MEMANGGIL STORED PROCEDURE
if (isset($_POST['hapus'])) {
    $id = $_POST['idsatuan'];
    mysqli_query($conn, "CALL sp_hapus_satuan('$id')");
    header("Location: satuan.php?msg=deleted");
    exit();
}

include 'header.php';
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php 
            if($_GET['msg'] == 'added') echo 'Data satuan berhasil ditambahkan!';
            elseif($_GET['msg'] == 'updated') echo 'Data satuan berhasil diperbarui!';
            elseif($_GET['msg'] == 'deleted') echo 'Data satuan berhasil dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold text-success"><i class="bi bi-tags-fill me-2"></i> Kelola Data Satuan</h5>
        <button type="button" class="btn btn-green fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Satuan
        </button>
    </div>
    <div class="card-body p-4">
        
        <div class="table-responsive">
            <table class="table table-hover table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nama Satuan</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 180px;">Aksi (CUD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = mysqli_query($conn, "SELECT * FROM satuan ORDER BY idsatuan ASC");
                    while ($row = mysqli_fetch_assoc($data)):
                    ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $row['idsatuan']; ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_satuan']); ?></td>
                        <td>
                            <?= $row['status']==1 
                                ? '<span class="badge badge-active"><i class="bi bi-check-circle me-1"></i>Aktif</span>' 
                                : '<span class="badge badge-inactive"><i class="bi bi-x-circle me-1"></i>Non-Aktif</span>'; ?>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-yellow me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['idsatuan']; ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['idsatuan']; ?>">
                                <i class="bi bi-trash3"></i> Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- MODAL EDIT SATUAN -->
                    <div class="modal fade" id="modalEdit<?= $row['idsatuan']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Form Edit Satuan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="satuan.php" method="POST">
                                    <div class="modal-body text-start">
                                        <input type="hidden" name="idsatuan" value="<?= $row['idsatuan']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Satuan</label>
                                            <input type="text" name="nama_satuan" class="form-control" value="<?= htmlspecialchars($row['nama_satuan']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="1" <?= ($row['status']==1)?'selected':''; ?>>Aktif</option>
                                                <option value="0" <?= ($row['status']==0)?'selected':''; ?>>Non-Aktif</option>
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

                    <!-- MODAL HAPUS SATUAN -->
                    <div class="modal fade" id="modalHapus<?= $row['idsatuan']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="satuan.php" method="POST">
                                    <input type="hidden" name="idsatuan" value="<?= $row['idsatuan']; ?>">
                                    <div class="modal-body text-center py-4">
                                        <p class="fs-5 mb-0">Apakah Anda yakin ingin menghapus satuan <strong><?= htmlspecialchars($row['nama_satuan']); ?></strong>?</p>
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

<!-- MODAL TAMBAH SATUAN -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Form Tambah Satuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="satuan.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Satuan</label>
                        <input type="text" name="nama_satuan" class="form-control" placeholder="Contoh: Pcs, Pack, Kg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
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