<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);

// =====================================
// FETCH SENARAI PROFIL PENGGUNA
// =====================================
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.id_userprofile,
            u.nama_user,
            u.jawatan_user,
            u.emel_user,
            u.notelefon_user,
            u.fax_user,
            b.bahagianunit
        FROM lookup_userprofile u
        LEFT JOIN lookup_bahagianunit b 
            ON u.id_bahagianunit = b.id_bahagianunit
        ORDER BY u.nama_user ASC
    ");
    $stmt->execute();
    $profil_pengguna_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $profil_pengguna_list = [];
    // Debug (jika perlu)
    // echo "SQL Error: " . $e->getMessage();
}

?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil Peralatan | Sistem Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    
    <link href="css/profil.css" rel="stylesheet">

    <!-- Skrip ringkas untuk handle delete, menggunakan modal UI jika anda mempunyainya -->
    <script>
        function confirmDelete(id, nama_user) {
            if (confirm(`Adakah anda pasti mahu memadam profil pengguna "${nama_user}"?`)) {
                // Dalam aplikasi sebenar, ini akan POST ke delete_pengguna.php
                console.log(`Menghantar permintaan padam untuk ID: ${id}`);
                // window.location.href = `delete_pengguna.php?id=${id}`; 
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
            <i class="bi bi-pc-display"></i> Senarai Profil Pengguna
        </div>

        <div class="profil-card shadow-sm p-4">

            <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
                <div class="alert alert-success">Profil pengguna berjaya dipadam.</div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Ralat: Gagal memadam profil pengguna.</div>
            <?php endif; ?>

            <?php if (empty($profil_pengguna_list)): ?>
                <div class="alert alert-info" role="alert">
                    Tiada profil pengguna yang ditemui dalam pangkalan data.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle sistem-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th style="width: 30%;">Nama Pengguna</th>
                                <th style="width: 10%;">Jawatan</th>
                                <th style="width: 30%;">Bahagian / Unit</th>
                                <th class="text-center" style="width: 10%;">Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($profil_pengguna_list as $profil): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>

                                    <td><?= htmlspecialchars($profil['nama_user']); ?></td>

                                    <td><?= htmlspecialchars($profil['jawatan_user']); ?></td>

                                    <td><?= htmlspecialchars($profil['bahagianunit'] ?? '-'); ?></td>

                                    <td class="text-center">

                                        <!-- VIEW -->
                                        <a href="view_pengguna.php?id=<?= $profil['id_userprofile']; ?>" 
                                        class="btn btn-outline-primary btn-sm me-1">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <!-- DELETE -->
                                        <button class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="<?= $profil['id_userprofile']; ?>"
                                                data-nama="<?= htmlspecialchars($profil['nama_user']); ?>">
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
            <h5 class="modal-title">Padam Profil Pengguna</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <p>Adakah anda pasti mahu memadam pengguna berikut:</p>
            <p><strong id="namaPengguna"></strong></p>
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

            document.getElementById('namaPengguna').textContent = nama;
            document.getElementById('confirmDeleteBtn').href = "delete_pengguna.php?id=" + id;
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
