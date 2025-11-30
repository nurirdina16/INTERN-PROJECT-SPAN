<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: profil_supplier.php");
    exit;
}

$id = intval($_GET['id']);
$error = null;
$success = null;

// ==========================
// FETCH PEMBEKAL BERDASARKAN ID
// ==========================
try {
    $stmt = $pdo->prepare("SELECT * FROM lookup_pembekal WHERE id_pembekal = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Rekod pembekal tidak dijumpai!");
    }

} catch (Exception $e) {
    die("Ralat pangkalan data: " . $e->getMessage());
}


// ==========================
// Jika ada id_PIC, ambil data PIC, kalau tiada set kosong
// ==========================
$dataPIC = [
    'id_PIC' => null,
    'nama_PIC' => '',
    'emel_PIC' => '',
    'notelefon_PIC' => '',
    'fax_PIC' => '',
    'jawatan_PIC' => ''
];

if (!empty($data['id_PIC'])) {
    try {
        $stmt2 = $pdo->prepare("SELECT * FROM lookup_pic WHERE id_PIC = ?");
        $stmt2->execute([$data['id_PIC']]);
        $f = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($f) {
            $dataPIC = $f;
        }
    } catch (Exception $e) {
        // jangan die; cuma warn
        $error = "Ralat ambil data PIC: " . $e->getMessage();
    }
}

// ==========================
// FETCH SENARAI PIC UNTUK DROPDOWN (optional jika anda mahu dropdown juga)
// ==========================
try {
    $stmtPIC = $pdo->prepare("SELECT id_PIC, nama_PIC FROM lookup_pic ORDER BY nama_PIC ASC");
    $stmtPIC->execute();
    $senaraiPIC = $stmtPIC->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $senaraiPIC = [];
}


// ==========================
// UPDATE JIKA POST
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // PEMBEKAL
    $nama_syarikat   = trim($_POST['nama_syarikat'] ?? '');
    $alamat_syarikat = trim($_POST['alamat_syarikat'] ?? '');
    $tempoh_kontrak  = trim($_POST['tempoh_kontrak'] ?? '');

    // id_PIC mungkin dihantar (hidden) atau kosong -> tangani
    $id_PIC_post = isset($_POST['id_PIC']) && $_POST['id_PIC'] !== '' ? intval($_POST['id_PIC']) : null;

    // PIC fields (user boleh edit atau masukkan baru)
    $nama_PIC       = trim($_POST['nama_PIC'] ?? '');
    $emel_PIC       = trim($_POST['emel_PIC'] ?? '');
    $notelefon_PIC  = trim($_POST['notelefon_PIC'] ?? '');
    $fax_PIC        = trim($_POST['fax_PIC'] ?? '');
    $jawatan_PIC    = trim($_POST['jawatan_PIC'] ?? '');

    // Simple validation
    if ($nama_syarikat === '') {
        $error = "Nama syarikat diperlukan.";
    }

    // Begin transaction: update pembekal dan PIC (insert/update) atomically
    if (empty($error)) {
        try {
            $pdo->beginTransaction();

            // 1) handle PIC:
            if ($id_PIC_post) {
                // update existing PIC
                // but ensure email uniqueness (exclude current id)
                if ($emel_PIC !== '') {
                    $chk = $pdo->prepare("SELECT id_PIC FROM lookup_pic WHERE emel_PIC = ? AND id_PIC != ?");
                    $chk->execute([$emel_PIC, $id_PIC_post]);
                    if ($chk->fetch()) {
                        throw new Exception("Emel PIC sudah wujud pada rekod lain.");
                    }
                }

                $updatePIC = $pdo->prepare("
                    UPDATE lookup_pic
                    SET nama_PIC = ?, emel_PIC = ?, notelefon_PIC = ?, fax_PIC = ?, jawatan_PIC = ?
                    WHERE id_PIC = ?
                ");
                $updatePIC->execute([
                    $nama_PIC,
                    $emel_PIC,
                    $notelefon_PIC,
                    $fax_PIC,
                    $jawatan_PIC,
                    $id_PIC_post
                ]);

                $final_id_PIC = $id_PIC_post;

            } else {
                // create new PIC only if user provided something (e.g., nama or email)
                if ($nama_PIC !== '' || $emel_PIC !== '' || $notelefon_PIC !== '' || $jawatan_PIC !== '') {
                    // ensure email uniqueness if provided
                    if ($emel_PIC !== '') {
                        $chk = $pdo->prepare("SELECT id_PIC FROM lookup_pic WHERE emel_PIC = ?");
                        $chk->execute([$emel_PIC]);
                        if ($chk->fetch()) {
                            throw new Exception("Emel PIC sudah wujud pada rekod lain. Sila pilih PIC sedia ada atau gunakan emel lain.");
                        }
                    }

                    $insertPIC = $pdo->prepare("
                        INSERT INTO lookup_pic (nama_PIC, emel_PIC, notelefon_PIC, fax_PIC, jawatan_PIC)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $insertPIC->execute([
                        $nama_PIC,
                        $emel_PIC,
                        $notelefon_PIC,
                        $fax_PIC,
                        $jawatan_PIC
                    ]);

                    $final_id_PIC = $pdo->lastInsertId();
                } else {
                    // user left PIC blank and didn't choose existing one
                    $final_id_PIC = null;
                }
            }

            // 2) Update pembekal (set id_PIC to final_id_PIC or NULL)
            $update = $pdo->prepare("
                UPDATE lookup_pembekal
                SET nama_syarikat = ?, alamat_syarikat = ?, tempoh_kontrak = ?, id_PIC = ?
                WHERE id_pembekal = ?
            ");

            // If final_id_PIC is null, pass NULL explicitly
            if ($final_id_PIC === null) {
                $update->bindValue(1, $nama_syarikat, PDO::PARAM_STR);
                $update->bindValue(2, $alamat_syarikat, PDO::PARAM_STR);
                $update->bindValue(3, $tempoh_kontrak, PDO::PARAM_STR);
                $update->bindValue(4, null, PDO::PARAM_NULL);
                $update->bindValue(5, $id, PDO::PARAM_INT);
                $update->execute();
            } else {
                $update->execute([
                    $nama_syarikat,
                    $alamat_syarikat,
                    $tempoh_kontrak,
                    $final_id_PIC,
                    $id
                ]);
            }

            $pdo->commit();

            header("Location: view_supplier.php?id=$id&success=updated");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            // show the actual DB error for debugging — change in production
            $error = "Ralat mengemas kini rekod pembekal: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kemaskini Pembekal | Sistem Profil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>

    <link href="css/profil.css" rel="stylesheet">
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<?php include 'header.php'; ?>

<div class="profil-card shadow-sm p-4">

    <div class="view-main-header">
        <div class="header-wrapper">
            <i class="bi bi-pencil-square"></i>
            <span>Kemaskini Pembekal</span>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">

        <!-- HANTAR ID PIC AS HIDDEN supaya server tahu kita edit existing PIC (jika ada) -->
        <input type="hidden" name="id_PIC" value="<?= htmlspecialchars($dataPIC['id_PIC'] ?? ''); ?>">

        <!-- MAKLUMAT PEMBEKAL -->
        <div class="view-section-box">
            <div class="view-section-title">MAKLUMAT PEMBEKAL</div>

            <div class="mb-3">
                <label class="form-label">Nama Syarikat</label>
                <input type="text" name="nama_syarikat" class="form-control" required
                       value="<?= htmlspecialchars($data['nama_syarikat']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Syarikat</label>
                <textarea name="alamat_syarikat" class="form-control" rows="3" required><?= htmlspecialchars($data['alamat_syarikat']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Tempoh Kontrak</label>
                <input type="text" name="tempoh_kontrak" class="form-control"
                       value="<?= htmlspecialchars($data['tempoh_kontrak']); ?>">
            </div>
        </div>

        <!-- MAKLUMAT PIC -->
        <div class="view-section-box">
            <div class="view-section-title">MAKLUMAT PIC</div>

            <!-- NAMA PIC -->
            <div class="mb-3">
                <label class="form-label">Nama PIC</label>
                <input type="text" name="nama_PIC" class="form-control"
                    value="<?= htmlspecialchars($dataPIC['nama_PIC'] ?? ''); ?>">
            </div>

            <!-- JAWATAN PIC -->
            <div class="mb-3">
                <label class="form-label">Jawatan PIC</label>
                <input type="text" name="jawatan_PIC" class="form-control"
                    value="<?= htmlspecialchars($dataPIC['jawatan_PIC'] ?? ''); ?>">
            </div>

            <!-- EMEL PIC -->
            <div class="mb-3">
                <label class="form-label">Emel PIC</label>
                <input type="email" name="emel_PIC" class="form-control"
                    value="<?= htmlspecialchars($dataPIC['emel_PIC'] ?? ''); ?>">
            </div>

            <!-- TELEFON PIC -->
            <div class="mb-3">
                <label class="form-label">No Telefon PIC</label>
                <input type="text" name="notelefon_PIC" class="form-control"
                    value="<?= htmlspecialchars($dataPIC['notelefon_PIC'] ?? ''); ?>">
            </div>

            <!-- FAX PIC -->
            <div class="mb-3">
                <label class="form-label">Fax PIC</label>
                <input type="text" name="fax_PIC" class="form-control"
                    value="<?= htmlspecialchars($dataPIC['fax_PIC'] ?? ''); ?>">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="view_supplier.php?id=<?= $id; ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>

    </form>

</div>
</div>

</body>
</html>
