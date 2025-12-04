<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// ===================================
// VALIDATE ID
// ===================================
if (!isset($_GET['id'])) {
    header("Location: profil_pengguna.php");
    exit;
}

$id = intval($_GET['id']);
$alert_type = '';
$alert_message = '';

// ===================================
// FETCH LOOKUP BAHAGIAN/UNIT
// ===================================
$bahagian_list = $pdo->query("SELECT * FROM lookup_bahagianunit ORDER BY bahagianunit ASC")
                     ->fetchAll(PDO::FETCH_ASSOC);

// ===================================
// FETCH DATA PENGGUNA
// ===================================
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.id_userprofile,
            u.nama_user,
            u.jawatan_user,
            u.emel_user,
            u.notelefon_user,
            u.fax_user,
            u.id_bahagianunit
        FROM lookup_userprofile u
        WHERE u.id_userprofile = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profil) {
        die("<div class='alert alert-danger m-4'>Rekod pengguna tidak ditemui!</div>");
    }

} catch (PDOException $e) {
    die("<div class='alert alert-danger m-4'>SQL Error: " . $e->getMessage() . "</div>");
}


// ===================================
// PROCESS UPDATE
// ===================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_user      = $_POST['nama_user'] ?? '';
    $jawatan_user   = $_POST['jawatan_user'] ?? '';
    $emel_user      = $_POST['emel_user'] ?? '';
    $notelefon_user = $_POST['notelefon_user'] ?? '';
    $fax_user       = $_POST['fax_user'] ?? '';
    $id_bahagianunit = intval($_POST['id_bahagianunit'] ?? 0);

    try {
        $update = $pdo->prepare("
            UPDATE lookup_userprofile
            SET 
                nama_user = :nama_user,
                jawatan_user = :jawatan_user,
                emel_user = :emel_user,
                notelefon_user = :notelefon_user,
                fax_user = :fax_user,
                id_bahagianunit = :id_bahagianunit
            WHERE id_userprofile = :id
        ");

        $update->execute([
            ':nama_user' => $nama_user,
            ':jawatan_user' => $jawatan_user,
            ':emel_user' => $emel_user,
            ':notelefon_user' => $notelefon_user,
            ':fax_user' => $fax_user,
            ':id_bahagianunit' => $id_bahagianunit,
            ':id' => $id
        ]);

        header("Location: view_pengguna.php?id=" . $id);
        exit;

    } catch (PDOException $e) {
        $alert_type = "danger";
        $alert_message = "SQL Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kemaskini Pengguna | Sistem Profil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>

    <link href="css/profil.css" rel="stylesheet">
</head>

<body>

    <!-- SIDEBAR -->
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- FIXED HEADER -->
        <div class="sticky-top bg-white py-2 mb-3 d-flex align-items-center justify-content-between shadow-sm px-3" style="z-index: 1050;">
            <div style="flex: 1;"><?php include 'header.php'; ?></div>
        </div>

        <div class="profil-card shadow-sm p-4">
            <div class="view-main-header">
                <div class="header-wrapper">
                    <i class="bi bi-pencil-square"></i>
                    <span>Kemaskini Pengguna</span>
                </div>
            </div>

            <?php if ($alert_message): ?>
                <div class="alert alert-<?= $alert_type; ?>"><?= $alert_message; ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nama Penuh</label>
                        <input type="text" name="nama_user" class="form-control"
                               value="<?= htmlspecialchars($profil['nama_user']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jawatan</label>
                        <input type="text" name="jawatan_user" class="form-control"
                               value="<?= htmlspecialchars($profil['jawatan_user']); ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Bahagian / Unit</label>
                        <select name="id_bahagianunit" class="form-control" required>
                            <option value="">-- Pilih Bahagian --</option>
                            <?php foreach ($bahagian_list as $b): ?>
                                <option value="<?= $b['id_bahagianunit']; ?>"
                                    <?= $profil['id_bahagianunit'] == $b['id_bahagianunit'] ? 'selected' : ''; ?>>
                                    <?= $b['bahagianunit']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Emel</label>
                        <input type="email" name="emel_user" class="form-control"
                               value="<?= htmlspecialchars($profil['emel_user']); ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">No. Telefon</label>
                        <input type="text" name="notelefon_user" class="form-control"
                               value="<?= htmlspecialchars($profil['notelefon_user']); ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">No. Faks</label>
                        <input type="text" name="fax_user" class="form-control"
                               value="<?= htmlspecialchars($profil['fax_user']); ?>">
                    </div>

                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="view_pengguna.php?id=<?= $id; ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>

    </div>

</body>
</html>
