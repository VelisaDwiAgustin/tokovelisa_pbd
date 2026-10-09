<?php
require_once 'koneksi.php';

// PROSES TAMBAH & EDIT MEMANGGIL STORED PROCEDURE
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_role']);

    if (isset($_POST['idrole']) && $_POST['idrole'] != '') {
        $id = $_POST['idrole'];
        mysqli_query($conn, "CALL sp_ubah_role('$id', '$nama')");
        header("Location: role.php?msg=updated");
    } else {
        mysqli_query($conn, "CALL sp_tambah_role('$nama')");
        header("Location: role.php?msg=added");
    }
    exit();
}

// PROSES HAPUS MEMANGGIL STORED PROCEDURE
if (isset($_POST['hapus'])) {
    $id = $_POST['idrole'];
    mysqli_query($conn, "CALL sp_hapus_role('$id')");
    header("Location: role.php?msg=deleted");
    exit();
}

include 'header.php';
?>

<!-- ALERT NOTIFIKASI SUKSES -->
<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php 
            if($_GET['msg'] == 'added') echo 'Data role berhasil ditambahkan!';
            elseif($_GET['msg'] == 'updated') echo 'Data role berhasil diperbarui!';
            elseif($_GET['msg'] == 'deleted') echo 'Data role berhasil dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold text-success"><i class="bi bi-shield-lock-fill me-2"></i> Kelola Data Role</h5>
        <button type="button" class="btn btn-green fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Role
        </button>
    </div>
    <div class="card-body p-4">
        
        <div class="table-responsive">
            <table class="table table-hover table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 100px;">ID Role</th>
                        <th>Nama Role</th>
                        <th class="text-center" style="width: 180px;">Aksi (CUD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = mysqli_query($conn, "SELECT * FROM role ORDER BY idrole ASC");
                    while ($row = mysqli_fetch_assoc($data)):
                    ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $row['idrole']; ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_role']); ?></td>
                        <td class="text-center">
                            <!-- TOMBOL TRIGGER MODAL EDIT -->
                            <button type="button" class="btn btn-sm btn-yellow me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['idrole']; ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <!-- TOMBOL TRIGGER MODAL HAPUS -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['idrole']; ?>">
                                <i class="bi bi-trash3"></i> Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- ========================================== -->
                    <!-- MODAL EDIT ROLE (UPDATE) -->
                    <!-- ========================================== -->
                    <div class="modal fade" id="modalEdit<?= $row['idrole']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Form Edit Role</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="role.php" method="POST">
                                    <div class="modal-body text-start">
                                        <input type="hidden" name="idrole" value="<?= $row['idrole']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Role</label>
                                            <input type="text" name="nama_role" class="form-control" value="<?= htmlspecialchars($row['nama_role']); ?>" required>
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

                    <!-- ========================================== -->
                    <!-- MODAL HAPUS ROLE (DELETE) -->
                    <!-- ========================================== -->
                    <div class="modal fade" id="modalHapus<?= $row['idrole']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="role.php" method="POST">
                                    <input type="hidden" name="idrole" value="<?= $row['idrole']; ?>">
                                    <div class="modal-body text-center py-4">
                                        <p class="fs-5 mb-0">Apakah Anda yakin ingin menghapus role <strong><?= htmlspecialchars($row['nama_role']); ?></strong>?</p>
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

<!-- ========================================== -->
<!-- MODAL TAMBAH ROLE (CREATE) -->
<!-- ========================================== -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Form Tambah Role</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="role.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Role</label>
                        <input type="text" name="nama_role" class="form-control" placeholder="Contoh: Admin, Kasir, Manager" required>
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