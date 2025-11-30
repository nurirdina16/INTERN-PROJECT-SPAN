<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);

// --- FETCH ALL PEMBEKAL ---
try {
    $stmt = $pdo->prepare("
        SELECT 
            P.id_pembekal, 
            P.nama_syarikat, 
            PIC.nama_PIC 
        FROM lookup_pembekal P
        LEFT JOIN lookup_pic PIC ON P.id_PIC = PIC.id_PIC
        ORDER BY P.nama_syarikat ASC
    ");
    $stmt->execute();
    $profil_pengguna_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $profil_pengguna_list = [];
}
?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil Supplier | Sistem Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    
    <link href="css/profil.css" rel="stylesheet">

    <!-- Skrip ringkas untuk handle delete, menggunakan modal UI jika anda mempunyainya -->
    <script>
        function confirmDelete(id, nama_syarikat) {
            if (confirm(`Adakah anda pasti mahu memadam profil pembekal "${nama_syarikat}"?`)) {
                // Dalam aplikasi sebenar, ini akan POST ke delete_supplier.php
                console.log(`Menghantar permintaan padam untuk ID: ${id}`);
                // window.location.href = `delete_supplier.php?id=${id}`; 
                alert('Fungsi Padam (Delete) belum diimplementasi. ID Profil: ' + id);
            }
        }
    </script>
</head>

<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- HEADER -->
        <?php include 'header.php'; ?>

        <div class="main-header mt-4 mb-3">
            <i class="bi bi-pc-display"></i> Senarai Profil Pembekal
        </div>

        <div class="profil-card shadow-sm p-4">

            <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
                <div class="alert alert-success">Profil pembekal berjaya dipadam.</div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                <?php 
                    if ($_GET['error'] == 'referenced') {
                        echo 'Ralat: Profil pembekal tidak boleh dipadam kerana ia masih merujuk kepada rekod sistem dan peralatan. Sila kemas kini rekod tersebut terlebih dahulu.';
                    } else {
                        echo 'Ralat: Gagal memadam profil pembekal.';
                    }
                ?>
                </div>
            <?php endif; ?>

            <?php if (empty($profil_pengguna_list)): ?>
                <div class="alert alert-info" role="alert">
                    Tiada profil pembekal yang ditemui dalam pangkalan data.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle sistem-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th style="width: 30%;">Nama Syarikat</th>
                                <th style="width: 15%;">Nama PIC</th>
                                <th class="text-center" style="width: 10%;">Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($profil_pengguna_list as $profil): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>

                                    <td><?= htmlspecialchars($profil['nama_syarikat']); ?></td>

                                    <td><?= htmlspecialchars($profil['nama_PIC']); ?></td>

                                    <td class="text-center">

                                        <!-- VIEW -->
                                        <a href="view_supplier.php?id=<?= $profil['id_pembekal']; ?>" 
                                        class="btn btn-outline-primary btn-sm me-1">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <!-- DELETE -->
                                        <button class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="<?= $profil['id_pembekal']; ?>"
                                                data-nama="<?= htmlspecialchars($profil['nama_syarikat']); ?>">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- DELETE FUNCTION -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        
        <div class="modal-header">
            <h5 class="modal-title">Padam Profil Pembekal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <p>Adakah anda pasti mahu memadam pembekal berikut:</p>
            <p><strong id="namaPembekal"></strong></p>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Padam</a>
        </div>

        </div>
    </div>
    </div>
    <script>
        const deleteModal = document.getElementById('deleteModal');

        deleteModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');

            document.getElementById('namaPembekal').textContent = nama;
            document.getElementById('confirmDeleteBtn').href = "delete_supplier.php?id=" + id;
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
