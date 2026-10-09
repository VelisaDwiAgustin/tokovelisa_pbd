<?php
require_once 'koneksi.php';

// PROSES TAMBAH & EDIT MEMANGGIL STORED PROCEDURE
if (isset($_POST['simpan'])) {
    $username   = mysqli_real_escape_string($conn, $_POST['username']);
    $idrole     = $_POST['idrole'];
    $pass_input = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';

    if (isset($_POST['iduser']) && $_POST['iduser'] != '') {
        $id = $_POST['iduser'];
        mysqli_query($conn, "CALL sp_ubah_user('$id', '$username', '$pass_input', '$idrole')");
        header("Location: user.php?msg=updated");
    } else {
        mysqli_query($conn, "CALL sp_tambah_user('$username', '$pass_input', '$idrole')");
        header("Location: user.php?msg=added");
    }
    exit();
}

// PROSES HAPUS MEMANGGIL STORED PROCEDURE
if (isset($_POST['hapus'])) {
    $id = $_POST['iduser'];
    mysqli_query($conn, "CALL sp_hapus_user('$id')");
    header("Location: user.php?msg=deleted");
    exit();
}

include 'header.php';
?>

<!-- ALERT NOTIFIKASI SUKSES -->
<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php 
            if($_GET['msg'] == 'added') echo 'Data user berhasil ditambahkan!';
            elseif($_GET['msg'] == 'updated') echo 'Data user berhasil diperbarui!';
            elseif($_GET['msg'] == 'deleted') echo 'Data user berhasil dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold text-success"><i class="bi bi-person-gear me-2"></i> Kelola Data User</h5>
        <button type="button" class="btn btn-green fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah User
        </button>
    </div>
    <div class="card-body p-4">
        
        <div class="table-responsive">
            <table class="table table-hover table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID User</th>
                        <th>Username</th>
                        <th>Role Akses</th>
                        <th class="text-center" style="width: 180px;">Aksi (CUD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = mysqli_query($conn, "SELECT user.*, role.nama_role FROM user JOIN role ON user.idrole = role.idrole ORDER BY iduser ASC");
                    while ($row = mysqli_fetch_assoc($data)):
                    ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $row['iduser']; ?></td>
                        <td class="fw-semibold"><i class="bi bi-person me-1 text-success"></i> <?= htmlspecialchars($row['username']); ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['nama_role']); ?></span></td>
                        <td class="text-center">
                            <!-- TOMBOL TRIGGER MODAL EDIT -->
                            <button type="button" class="btn btn-sm btn-yellow me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['iduser']; ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <!-- TOMBOL TRIGGER MODAL HAPUS -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['iduser']; ?>">
                                <i class="bi bi-trash3"></i> Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- ========================================== -->
                    <!-- MODAL EDIT USER (UPDATE) -->
                    <!-- ========================================== -->
                    <div class="modal fade" id="modalEdit<?= $row['iduser']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Form Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="user.php" method="POST">
                                    <div class="modal-body text-start">
                                        <input type="hidden" name="iduser" value="<?= $row['iduser']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Username</label>
                                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                                            <input type="password" name="password" class="form-control" placeholder="••••••••">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Role Akses</label>
                                            <select name="idrole" class="form-select" required>
                                                <?php
                                                $roles = mysqli_query($conn, "SELECT * FROM role");
                                                while ($r = mysqli_fetch_assoc($roles)) {
                                                    $selected = ($row['idrole'] == $r['idrole']) ? 'selected' : '';
                                                    echo "<option value='{$r['idrole']}' $selected>{$r['nama_role']}</option>";
                                                }
                                                ?>
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

                    <!-- ========================================== -->
                    <!-- MODAL HAPUS USER (DELETE) -->
                    <!-- ========================================== -->
                    <div class="modal fade" id="modalHapus<?= $row['iduser']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="user.php" method="POST">
                                    <input type="hidden" name="iduser" value="<?= $row['iduser']; ?>">
                                    <div class="modal-body text-center py-4">
                                        <p class="fs-5 mb-0">Apakah Anda yakin ingin menghapus user <strong><?= htmlspecialchars($row['username']); ?></strong>?</p>
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
<!-- MODAL TAMBAH USER (CREATE) -->
<!-- ========================================== -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Form Tambah User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="user.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username login" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Akses</label>
                        <select name="idrole" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            <?php
                            $roles = mysqli_query($conn, "SELECT * FROM role");
                            while ($r = mysqli_fetch_assoc($roles)) {
                                echo "<option value='{$r['idrole']}'>{$r['nama_role']}</option>";
                            }
                            ?>
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