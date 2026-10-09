<?php
require_once 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['nama_vendor']);
    $badan_hukum = $_POST['badan_hukum'];
    $status      = $_POST['status'];

    if (isset($_POST['idvendor']) && $_POST['idvendor'] != '') {
        $id = $_POST['idvendor'];
        mysqli_query($conn, "CALL sp_ubah_vendor('$id', '$nama', '$badan_hukum', '$status')");
        header("Location: vendor.php?msg=updated");
    } else {
        mysqli_query($conn, "CALL sp_tambah_vendor('$nama', '$badan_hukum', '$status')");
        header("Location: vendor.php?msg=added");
    }
    exit();
}

if (isset($_POST['hapus'])) {
    $id = $_POST['idvendor'];
    mysqli_query($conn, "CALL sp_hapus_vendor('$id')");
    header("Location: vendor.php?msg=deleted");
    exit();
}

include 'header.php';
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php 
            if($_GET['msg'] == 'added') echo 'Data vendor berhasil ditambahkan!';
            elseif($_GET['msg'] == 'updated') echo 'Data vendor berhasil diperbarui!';
            elseif($_GET['msg'] == 'deleted') echo 'Data vendor berhasil dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold text-success"><i class="bi bi-truck-flatbed me-2"></i> Kelola Data Vendor</h5>
        <button type="button" class="btn btn-green fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Vendor
        </button>
    </div>
    <div class="card-body p-4">
        
        <div class="table-responsive">
            <table class="table table-hover table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nama Vendor</th>
                        <th>Badan Hukum</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 180px;">Aksi (CUD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = mysqli_query($conn, "SELECT * FROM vendor ORDER BY idvendor ASC");
                    while ($row = mysqli_fetch_assoc($data)):
                    ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $row['idvendor']; ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_vendor']); ?></td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <?= $row['badan_hukum']=='P'?'PT':($row['badan_hukum']=='C'?'CV':'UD'); ?>
                            </span>
                        </td>
                        <td>
                            <?= $row['status']=='1' 
                                ? '<span class="badge badge-active"><i class="bi bi-check-circle me-1"></i>Aktif</span>' 
                                : '<span class="badge badge-inactive"><i class="bi bi-x-circle me-1"></i>Non-Aktif</span>'; ?>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-yellow me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['idvendor']; ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['idvendor']; ?>">
                                <i class="bi bi-trash3"></i> Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- MODAL EDIT VENDOR -->
                    <div class="modal fade" id="modalEdit<?= $row['idvendor']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Form Edit Vendor</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="vendor.php" method="POST">
                                    <div class="modal-body text-start">
                                        <input type="hidden" name="idvendor" value="<?= $row['idvendor']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Vendor</label>
                                            <input type="text" name="nama_vendor" class="form-control" value="<?= htmlspecialchars($row['nama_vendor']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Badan Hukum</label>
                                            <select name="badan_hukum" class="form-select" required>
                                                <option value="P" <?= ($row['badan_hukum']=='P')?'selected':''; ?>>PT (Perseroan Terbatas)</option>
                                                <option value="C" <?= ($row['badan_hukum']=='C')?'selected':''; ?>>CV (Commanditaire Vennootschap)</option>
                                                <option value="U" <?= ($row['badan_hukum']=='U')?'selected':''; ?>>UD (Usaha Dagang)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="1" <?= ($row['status']=='1')?'selected':''; ?>>Aktif</option>
                                                <option value="0" <?= ($row['status']=='0')?'selected':''; ?>>Non-Aktif</option>
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

                    <!-- MODAL HAPUS VENDOR -->
                    <div class="modal fade" id="modalHapus<?= $row['idvendor']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="vendor.php" method="POST">
                                    <input type="hidden" name="idvendor" value="<?= $row['idvendor']; ?>">
                                    <div class="modal-body text-center py-4">
                                        <p class="fs-5 mb-0">Apakah Anda yakin ingin menghapus vendor <strong><?= htmlspecialchars($row['nama_vendor']); ?></strong>?</p>
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

<!-- MODAL TAMBAH VENDOR -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Form Tambah Vendor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="vendor.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Vendor</label>
                        <input type="text" name="nama_vendor" class="form-control" placeholder="Nama PT/CV" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Badan Hukum</label>
                        <select name="badan_hukum" class="form-select" required>
                            <option value="P">PT (Perseroan Terbatas)</option>
                            <option value="C">CV (Commanditaire Vennootschap)</option>
                            <option value="U">UD (Usaha Dagang)</option>
                        </select>
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