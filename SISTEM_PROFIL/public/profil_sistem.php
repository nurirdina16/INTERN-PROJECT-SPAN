<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);

// 1. Dapatkan id_jenisprofil untuk 'SISTEM'
$id_jenisprofil_sistem = null;
try {
    $stmt = $pdo->prepare("SELECT id_jenisprofil FROM LOOKUP_JENISPROFIL WHERE jenisprofil = :jenisprofil");
    $stmt->execute([':jenisprofil' => 'SISTEM']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $id_jenisprofil_sistem = $result['id_jenisprofil'];
    } else {
        // Jika 'SISTEM' tidak wujud dalam lookup table
        echo "<div class='alert alert-warning' role='alert'>Jenis Profil 'SISTEM' tidak ditemui dalam pangkalan data.</div>";
    }
} catch (PDOException $e) {
    die("Ralat mencari Jenis Profil: " . $e->getMessage());
}

$profil_sistem_list = [];

// 2. Ambil data Profil Sistem
if ($id_jenisprofil_sistem !== null) {
    try {
        // Query untuk mendapatkan data dari PROFIL, SISTEM, dan LOOKUP_BAHAGIANUNIT
        $sql = "
            SELECT
                P.id_profilsistem,
                S.nama_sistem,
                LS.status,
                P.tarikh_kemaskini
            FROM
                PROFIL P
            INNER JOIN
                SISTEM S ON P.id_profilsistem = S.id_profilsistem
            LEFT JOIN
                LOOKUP_STATUS LS ON P.id_status = LS.id_status
            WHERE
                P.id_jenisprofil = :id_jenisprofil_sistem
            ORDER BY
                S.nama_sistem ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_jenisprofil_sistem' => $id_jenisprofil_sistem]);
        $profil_sistem_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Profil Sistem | Sistem Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    
    <link href="css/profil.css" rel="stylesheet">

    <!-- Skrip ringkas untuk handle delete, menggunakan modal UI jika anda mempunyainya -->
    <script>
        function confirmDelete(id, nama_sistem) {
            if (confirm(`Adakah anda pasti mahu memadam profil sistem "${nama_sistem}"?`)) {
                // Dalam aplikasi sebenar, ini akan POST ke delete_sistem.php
                console.log(`Menghantar permintaan padam untuk ID: ${id}`);
                // window.location.href = `delete_sistem.php?id=${id}`; 
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
            <i class="bi bi-pc-display"></i> Senarai Profil Sistem
        </div>

        <div class="profil-card shadow-sm p-4">
            <?php if (empty($profil_sistem_list)): ?>
                <div class="alert alert-info" role="alert">
                    Tiada profil sistem yang ditemui dalam pangkalan data.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle sistem-table">
                        <thead>
                            <tr>
                                <th class="text-center" scope="col" style="width: 5%;">#</th>
                                <th scope="col" style="width: 50%;">Nama Sistem</th>
                                <th class="text-center" scope="col" style="width: 10%;">Status</th>
                                <th class="text-center" scope="col" style="width: 12%;">Tarikh Kemaskini</th>
                                <th class="text-center" scope="col" style="width: 10%;" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($profil_sistem_list as $profil): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($profil['nama_sistem']); ?></td>

                                    <td class="text-center">
                                        <span class="<?= get_status_badge_class($profil['status']); ?>">
                                            <?= htmlspecialchars($profil['status'] ?: 'Tiada Status'); ?>
                                        </span>
                                    </td>

                                    <td class="text-center"><?= format_tarikh($profil['tarikh_kemaskini']); ?></td>

                                    <td class="text-center">
                                        <a href="view_sistem.php?id=<?= $profil['id_profilsistem']; ?>" 
                                        class="btn btn-outline-primary btn-sm me-1">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <button onclick="confirmDelete(<?= $profil['id_profilsistem']; ?>, '<?= addslashes($profil['nama_sistem']); ?>')" 
                                                class="btn btn-outline-danger btn-sm">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
