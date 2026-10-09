<?php
require_once 'koneksi.php';

// PROSES TAMBAH & EDIT MEMANGGIL STORED PROCEDURE
if (isset($_POST['simpan'])) {
    $jenis    = $_POST['jenis'];
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $idsatuan = $_POST['idsatuan'];
    $status   = $_POST['status'];
    $harga    = $_POST['harga'];

    if (isset($_POST['idbarang']) && $_POST['idbarang'] != '') {
        $id = $_POST['idbarang'];
        // CALL SP UBAH
        mysqli_query($conn, "CALL sp_ubah_barang('$id', '$jenis', '$nama', '$idsatuan', '$status', '$harga')");
        header("Location: barang.php?msg=updated");
    } else {
        // CALL SP TAMBAH
        mysqli_query($conn, "CALL sp_tambah_barang('$jenis', '$nama', '$idsatuan', '$status', '$harga')");
        header("Location: barang.php?msg=added");
    }
    exit();
}

// PROSES HAPUS MEMANGGIL STORED PROCEDURE
if (isset($_POST['hapus'])) {
    $id = $_POST['idbarang'];
    // CALL SP HAPUS
    mysqli_query($conn, "CALL sp_hapus_barang('$id')");
    header("Location: barang.php?msg=deleted");
    exit();
}

include 'header.php';
?>

<!-- ALERT NOTIFIKASI SUKSES -->
<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php 
            if($_GET['msg'] == 'added') echo 'Data barang berhasil ditambahkan!';
            elseif($_GET['msg'] == 'updated') echo 'Data barang berhasil diperbarui!';
            elseif($_GET['msg'] == 'deleted') echo 'Data barang berhasil dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold text-success"><i class="bi bi-box-seam-fill me-2"></i> Kelola Data Barang</h5>
        <button type="button" class="btn btn-green fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Barang
        </button>
    </div>
    <div class="card-body p-4">
        
        <div class="table-responsive">
            <table class="table table-hover table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 180px;">Aksi (CUD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = mysqli_query($conn, "SELECT barang.*, satuan.nama_satuan FROM barang JOIN satuan ON barang.idsatuan = satuan.idsatuan ORDER BY idbarang ASC");
                    while ($row = mysqli_fetch_assoc($data)):
                    ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $row['idbarang']; ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama']); ?></td>
                        <td><span class="badge bg-secondary"><?= $row['jenis']; ?></span></td>
                        <td><?= htmlspecialchars($row['nama_satuan']); ?></td>
                        <td class="fw-bold text-success">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td>
                            <?= $row['status']==1 
                                ? '<span class="badge badge-active"><i class="bi bi-check-circle me-1"></i>Aktif</span>' 
                                : '<span class="badge badge-inactive"><i class="bi bi-x-circle me-1"></i>Non-Aktif</span>'; ?>
                        </td>
                        <td class="text-center">
                            <!-- TOMBOL TRIGGER MODAL EDIT -->
                            <button type="button" class="btn btn-sm btn-yellow me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['idbarang']; ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <!-- TOMBOL TRIGGER MODAL HAPUS -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['idbarang']; ?>">
                                <i class="bi bi-trash3"></i> Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- ========================================== -->
                    <!-- MODAL EDIT BARANG (UPDATE) -->
                    <!-- ========================================== -->
                    <div class="modal fade" id="modalEdit<?= $row['idbarang']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Form Edit Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="barang.php" method="POST">
                                    <div class="modal-body text-start">
                                        <input type="hidden" name="idbarang" value="<?= $row['idbarang']; ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Barang</label>
                                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Jenis</label>
                                            <select name="jenis" class="form-select" required>
                                                <option value="B" <?= ($row['jenis']=='B')?'selected':''; ?>>B (Bahan)</option>
                                                <option value="M" <?= ($row['jenis']=='M')?'selected':''; ?>>M (Makanan)</option>
                                                <option value="N" <?= ($row['jenis']=='N')?'selected':''; ?>>N (Minuman)</option>
                                                <option value="P" <?= ($row['jenis']=='P')?'selected':''; ?>>P (Pembersih)</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Satuan</label>
                                            <select name="idsatuan" class="form-select" required>
                                                <?php
                                                $sat = mysqli_query($conn, "SELECT * FROM satuan WHERE status=1");
                                                while ($s = mysqli_fetch_assoc($sat)) {
                                                    $selected = ($row['idsatuan'] == $s['idsatuan']) ? 'selected' : '';
                                                    echo "<option value='{$s['idsatuan']}' $selected>{$s['nama_satuan']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Harga (Rp)</label>
                                            <input type="number" name="harga" class="form-control" value="<?= $row['harga']; ?>" required>
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

                    <!-- ========================================== -->
                    <!-- MODAL HAPUS BARANG (DELETE) -->
                    <!-- ========================================== -->
                    <div class="modal fade" id="modalHapus<?= $row['idbarang']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="barang.php" method="POST">
                                    <input type="hidden" name="idbarang" value="<?= $row['idbarang']; ?>">
                                    <div class="modal-body text-center py-4">
                                        <p class="fs-5 mb-0">Apakah Anda yakin ingin menghapus barang <strong><?= htmlspecialchars($row['nama']); ?></strong>?</p>
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
<!-- MODAL TAMBAH BARANG (CREATE) -->
<!-- ========================================== -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Form Tambah Barang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="barang.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Barang</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Kecap bango" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis</label>
                        <select name="jenis" class="form-select" required>
                            <option value="B">B (Bahan)</option>
                            <option value="M">M (Makanan)</option>
                            <option value="N">N (Minuman)</option>
                            <option value="P">P (Pembersih)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Satuan</label>
                        <select name="idsatuan" class="form-select" required>
                            <option value="">-- Pilih Satuan --</option>
                            <?php
                            $sat = mysqli_query($conn, "SELECT * FROM satuan WHERE status=1");
                            while ($s = mysqli_fetch_assoc($sat)) {
                                echo "<option value='{$s['idsatuan']}'>{$s['nama_satuan']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="Contoh: 10000" required>
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