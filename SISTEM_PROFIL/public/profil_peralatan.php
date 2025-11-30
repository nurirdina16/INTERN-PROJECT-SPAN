<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);

// 1. Dapatkan id_jenisprofil untuk 'PERALATAN'
$id_jenisprofil_peralatan = null;
try {
    $stmt = $pdo->prepare("SELECT id_jenisprofil FROM LOOKUP_JENISPROFIL WHERE jenisprofil = :jenisprofil");
    $stmt->execute([':jenisprofil' => 'PERALATAN']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $id_jenisprofil_peralatan = $result['id_jenisprofil'];
    } else {
        // Jika 'PERALATAN' tidak wujud dalam lookup table
        echo "<div class='alert alert-warning' role='alert'>Jenis Profil 'PERALATAN' tidak ditemui dalam pangkalan data.</div>";
    }
} catch (PDOException $e) {
    die("Ralat mencari Jenis Profil: " . $e->getMessage());
}

$profil_peralatan_list = [];

// 2. Ambil data Profil Peralatan
if ($id_jenisprofil_peralatan !== null) {
    try {
        // Query untuk mendapatkan data
        $sql = "
            SELECT 
                p.id_profilsistem,
                pr.nama_peralatan,
                ls.status AS status,
                p.tarikh_kemaskini
            FROM PROFIL p
            INNER JOIN PERALATAN pr 
                ON p.id_profilsistem = pr.id_profilsistem
            LEFT JOIN lookup_status ls
                ON p.id_status = ls.id_status
            WHERE p.id_jenisprofil = :id_jenisprofil_peralatan
            ORDER BY pr.nama_peralatan ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_jenisprofil_peralatan' => $id_jenisprofil_peralatan]);
        $profil_peralatan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger' role='alert'>Ralat Pangkalan Data (Data Profil): Gagal mengambil data profil sistem.</div>";
        exit;
    }
}

// Fungsi untuk format tarikh (jika perlu)
function format_tarikh($date) {
    if ($date && $date !== '0000-00-00') {
        return date('d/m/Y', strtotime($date));
    }
    return '-';
}

function get_status_badge_class($status) {
    switch (strtoupper($status)) {
        case 'AKTIF': return 'status-badge bg-success';
        case 'DALAM PERANCANGAN': return 'status-badge bg-warning text-dark';
        case 'TIDAK AKTIF': return 'status-badge bg-danger';
        default: return 'status-badge bg-secondary';
    }
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
        function confirmDelete(id, nama_sistem) {
            if (confirm(`Adakah anda pasti mahu memadam profil peralatan "${nama_peralatan}"?`)) {
                // Dalam aplikasi sebenar, ini akan POST ke delete_peralatan.php
                console.log(`Menghantar permintaan padam untuk ID: ${id}`);
                // window.location.href = `delete_peralatan.php?id=${id}`; 
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
            <i class="bi bi-pc-display"></i> Senarai Profil Peralatan
        </div>

        <div class="profil-card shadow-sm p-4">

            <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
                <div class="alert alert-success">Profil peralatan berjaya dipadam.</div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Ralat: Gagal memadam profil peralatan.</div>
            <?php endif; ?>

            <?php if (empty($profil_peralatan_list)): ?>
                <div class="alert alert-info" role="alert">
                    Tiada profil peralatan yang ditemui dalam pangkalan data.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle sistem-table">
                        <thead>
                            <tr>
                                <th class="text-center" scope="col" style="width: 5%;">#</th>
                                <th scope="col" style="width: 50%;">Nama Peralatan</th>
                                <th class="text-center" scope="col" style="width: 10%;">Status</th>
                                <th class="text-center" scope="col" style="width: 12%;">Tarikh Kemaskini</th>
                                <th class="text-center" scope="col" style="width: 10%;" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($profil_peralatan_list as $profil): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($profil['nama_peralatan']); ?></td>

                                    <td class="text-center">
                                        <span class="<?= get_status_badge_class($profil['status']); ?>">
                                            <?= htmlspecialchars($profil['status'] ?: 'Tiada Status'); ?>
                                        </span>
                                    </td>

                                    <td class="text-center"><?= format_tarikh($profil['tarikh_kemaskini']); ?></td>

                                    <td class="text-center">
                                        <a href="view_peralatan.php?id=<?= $profil['id_profilsistem']; ?>" 
                                        class="btn btn-outline-primary btn-sm me-1">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <button class="btn btn-outline-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal"
                                                data-id="<?= $profil['id_profilsistem']; ?>"
                                                data-nama="<?= htmlspecialchars($profil['nama_peralatan']); ?>">
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
            <h5 class="modal-title">Padam Profil Peralatan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <p>Adakah anda pasti mahu memadam peralatan berikut:</p>
            <p><strong id="namaPeralatan"></strong></p>
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

            document.getElementById('namaPeralatan').textContent = nama;
            document.getElementById('confirmDeleteBtn').href = "delete_peralatan.php?id=" + id;
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
