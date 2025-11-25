<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid ID.");
}

$id = intval($_GET['id']);

// Fetch main records
$profil = $pdo->query("SELECT * FROM PROFIL_SISTEM WHERE id_profilsistem = $id")->fetch(PDO::FETCH_ASSOC);
$sistem = $pdo->query("SELECT * FROM SISTEM WHERE id_profilsistem = $id")->fetch(PDO::FETCH_ASSOC);
$akses_list = $pdo->query("SELECT * FROM AKSES WHERE id_profilsistem = $id")->fetchAll(PDO::FETCH_ASSOC);
$entiti_list = $pdo->query("SELECT * FROM ENTITI WHERE id_profilsistem = $id")->fetchAll(PDO::FETCH_ASSOC);
$peg = $pdo->query("SELECT prs.*, lup.nama_user, lup.jawatan_user, lup.id_bahagianunit, lup.emel_user, lup.notelefon_user, lup.fax_user 
                    FROM PEGAWAI_RUJUKAN_SISTEM prs
                    LEFT JOIN LOOKUP_USERPROFILE lup ON prs.id_userprofile = lup.id_userprofile
                    WHERE prs.id_profilsistem = $id")->fetch(PDO::FETCH_ASSOC);

// Lookup data
$statuses = $pdo->query("SELECT * FROM LOOKUP_STATUS")->fetchAll(PDO::FETCH_ASSOC);
$jenisprofils = $pdo->query("SELECT * FROM LOOKUP_JENISPROFIL")->fetchAll(PDO::FETCH_ASSOC);
$bahagianunits = $pdo->query("SELECT * FROM LOOKUP_BAHAGIANUNIT")->fetchAll(PDO::FETCH_ASSOC);
$kategori = $pdo->query("SELECT * FROM LOOKUP_KATEGORI")->fetchAll(PDO::FETCH_ASSOC);
$kaedahpembangunan = $pdo->query("SELECT * FROM LOOKUP_KAEDAHPEMBANGUNAN")->fetchAll(PDO::FETCH_ASSOC);
$carta_list = $pdo->query("SELECT * FROM LOOKUP_CARTA")->fetchAll(PDO::FETCH_ASSOC);
$userprofiles = $pdo->query("SELECT * FROM LOOKUP_USERPROFILE")->fetchAll(PDO::FETCH_ASSOC);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // PROFIL SISTEM
    $stmt = $pdo->prepare("UPDATE PROFIL_SISTEM SET id_status=?, id_jenisprofil=? WHERE id_profilsistem=?");
    $stmt->execute([$_POST['id_status'], $_POST['id_jenisprofil'], $id]);

    // SISTEM
    $stmt = $pdo->prepare("UPDATE SISTEM SET nama_sistem=?, pemilik_sistem=?, id_kategori=?, objektif=?, tarikh_mula=?, tarikh_siap=?, tarikh_guna=?, bil_pengguna=?, bil_modul=?, id_kaedahPembangunan=? WHERE id_profilsistem=?");
    $stmt->execute([
        $_POST['nama_sistem'], $_POST['pemilik_sistem'], $_POST['id_kategori'], $_POST['objektif'],
        $_POST['tarikh_mula'], $_POST['tarikh_siap'], $_POST['tarikh_guna'],
        $_POST['bil_pengguna'], $_POST['bil_modul'], $_POST['id_kaedahPembangunan'], $id
    ]);

    // AKSES
    foreach ($akses_list as $a) {
        $id_akses = $a['id_akses'];
        $stmt = $pdo->prepare("UPDATE AKSES SET id_bahagianunit=?, id_kategoriuser=? WHERE id_akses=?");
        // Determine kategoriuser: 1 = dalaman, 2 = umum, 3 = both
        $kategoriuser = 0;
        if(isset($_POST['jenis_dalaman_'.$id_akses]) && isset($_POST['jenis_umum_'.$id_akses])) $kategoriuser = 3;
        elseif(isset($_POST['jenis_dalaman_'.$id_akses])) $kategoriuser = 1;
        elseif(isset($_POST['jenis_umum_'.$id_akses])) $kategoriuser = 2;

        $stmt->execute([
            $_POST['akses_bahagian_'.$id_akses],
            $kategoriuser,
            $id_akses
        ]);
    }

    // ENTITI
    foreach ($entiti_list as $e) {
        $id_entiti = $e['id_entiti'];
        $stmt = $pdo->prepare("UPDATE ENTITI SET nama_entiti=?, id_bahagianunit=?, id_userprofile=?, cio=?, ictso=?, id_carta=? WHERE id_entiti=?");
        $stmt->execute([
            $_POST['nama_entiti_'.$id_entiti],
            $_POST['bahagian_entiti_'.$id_entiti],
            $_POST['ketua_'.$id_entiti],
            $_POST['cio_'.$id_entiti],
            $_POST['ictso_'.$id_entiti],
            $_POST['carta_'.$id_entiti],
            $id_entiti
        ]);
    }

    // PEGAWAI RUJUKAN
    if ($peg) {
        $stmt = $pdo->prepare("UPDATE PEGAWAI_RUJUKAN_SISTEM SET id_userprofile=? WHERE id_rujukansistem=?");
        $stmt->execute([
            $_POST['nama_userprofile'],
            $peg['id_rujukansistem']
        ]);
    }

    $message = "Rekod berjaya dikemaskini.";
}
?>


<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
    <title>Kemaskini Sistem | Profil Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/profil.css">
</head>

<body>

    <div class="container-fluid py-4">
        <!-- FIXED HEADER + HOME -->
        <div class="sticky-top bg-white py-2 mb-3 d-flex align-items-center justify-content-between shadow-sm px-3" style="z-index: 1050;">
            <a href="profil_sistem.php" class="btn btn-secondary">
                <i class="bi bi-house-door"></i>
            </a>
            <div style="flex: 1;"><?php include 'header.php'; ?></div>
        </div>

        <div class="card profil-card shadow-sm p-4">
            <div class="title-section mb-4">Kemaskini Sistem</div>
            <?php if($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">
                <!-- PROFIL SISTEM -->
                <h5>Maklumat Profil</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Status</label>
                        <select name="id_status" class="form-select">
                            <?php foreach($statuses as $s): ?>
                                <option value="<?= $s['id_status'] ?>" <?= $profil['id_status']==$s['id_status']?'selected':'' ?>><?= $s['status'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Jenis Profil</label>
                        <select name="id_jenisprofil" class="form-select">
                            <?php foreach($jenisprofils as $j): ?>
                                <option value="<?= $j['id_jenisprofil'] ?>" <?= $profil['id_jenisprofil']==$j['id_jenisprofil']?'selected':'' ?>><?= $j['jenisprofil'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <!-- SISTEM -->
                <h5 class="mt-4">Maklumat Sistem</h5>
                <div class="mb-3"><label>Nama Sistem</label><input type="text" name="nama_sistem" class="form-control" value="<?= htmlspecialchars($sistem['nama_sistem'] ?? '') ?>"></div>
                <div class="mb-3"><label>Pemilik Sistem</label>
                    <select name="pemilik_sistem" class="form-select">
                        <?php foreach($bahagianunits as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>" <?= $sistem['pemilik_sistem']==$b['id_bahagianunit']?'selected':'' ?>><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3"><label>Kategori</label>
                    <select name="id_kategori" class="form-select">
                        <?php foreach($kategori as $k): ?>
                            <option value="<?= $k['id_kategori'] ?>" <?= $sistem['id_kategori']==$k['id_kategori']?'selected':'' ?>><?= $k['kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3"><label>Objektif</label><textarea name="objektif" class="form-control"><?= htmlspecialchars($sistem['objektif'] ?? '') ?></textarea></div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>Tarikh Mula</label><input type="date" name="tarikh_mula" class="form-control" value="<?= $sistem['tarikh_mula'] ?>"></div>
                    <div class="col-md-4 mb-3"><label>Tarikh Siap</label><input type="date" name="tarikh_siap" class="form-control" value="<?= $sistem['tarikh_siap'] ?>"></div>
                    <div class="col-md-4 mb-3"><label>Tarikh Guna</label><input type="date" name="tarikh_guna" class="form-control" value="<?= $sistem['tarikh_guna'] ?>"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label>Bilangan Pengguna</label><input type="number" name="bil_pengguna" class="form-control" value="<?= $sistem['bil_pengguna'] ?>"></div>
                    <div class="col-md-6 mb-3"><label>Bilangan Modul</label><input type="number" name="bil_modul" class="form-control" value="<?= $sistem['bil_modul'] ?>"></div>
                </div>
                <div class="mb-3"><label>Kaedah Pembangunan</label>
                    <select name="id_kaedahPembangunan" class="form-select">
                        <?php foreach($kaedahpembangunan as $kp): ?>
                            <option value="<?= $kp['id_kaedahPembangunan'] ?>" <?= $sistem['id_kaedahPembangunan']==$kp['id_kaedahPembangunan']?'selected':'' ?>><?= $kp['kaedahPembangunan'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <!-- AKSES SISTEM -->
                <h5 class="mt-4">Akses Sistem</h5>
                <?php foreach($akses_list as $a): ?>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label>Bahagian/Unit</label>
                            <select name="akses_bahagian_<?= $a['id_akses'] ?>" class="form-select">
                                <?php foreach($bahagianunits as $b): ?>
                                    <option value="<?= $b['id_bahagianunit'] ?>" <?= $a['id_bahagianunit']==$b['id_bahagianunit']?'selected':'' ?>><?= $b['bahagianunit'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2"><label>Dalaman</label><input type="checkbox" name="jenis_dalaman_<?= $a['id_akses'] ?>" <?= $a['id_kategoriuser']==1 || $a['id_kategoriuser']==3 ?'checked':'' ?>></div>
                        <div class="col-md-2"><label>Umum</label><input type="checkbox" name="jenis_umum_<?= $a['id_akses'] ?>" <?= $a['id_kategoriuser']==2 || $a['id_kategoriuser']==3 ?'checked':'' ?>></div>
                    </div>
                <?php endforeach; ?>


                <!-- ENTITI -->
                <h5 class="mt-4">Entiti</h5>
                <?php foreach($entiti_list as $e): ?>
                    <div class="row mb-2">
                        <div class="col-md-3"><label>Nama Entiti</label><input type="text" name="nama_entiti_<?= $e['id_entiti'] ?>" class="form-control" value="<?= $e['nama_entiti'] ?>"></div>
                        <div class="col-md-3"><label>Bahagian/Unit</label>
                            <select name="bahagian_entiti_<?= $e['id_entiti'] ?>" class="form-select">
                                <?php foreach($bahagianunits as $b): ?>
                                    <option value="<?= $b['id_bahagianunit'] ?>" <?= $e['id_bahagianunit']==$b['id_bahagianunit']?'selected':'' ?>><?= $b['bahagianunit'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2"><label>Ketua</label>
                            <select name="ketua_<?= $e['id_entiti'] ?>" class="form-select">
                                <?php foreach($userprofiles as $u): ?>
                                    <option value="<?= $u['id_userprofile'] ?>" <?= $e['id_userprofile']==$u['id_userprofile']?'selected':'' ?>><?= $u['nama_user'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2"><label>CIO</label>
                            <select name="cio_<?= $e['id_entiti'] ?>" class="form-select">
                                <?php foreach($userprofiles as $u): ?>
                                    <option value="<?= $u['id_userprofile'] ?>" <?= $e['cio']==$u['id_userprofile']?'selected':'' ?>><?= $u['nama_user'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2"><label>ICTSO</label>
                            <select name="ictso_<?= $e['id_entiti'] ?>" class="form-select">
                                <?php foreach($userprofiles as $u): ?>
                                    <option value="<?= $u['id_userprofile'] ?>" <?= $e['ictso']==$u['id_userprofile']?'selected':'' ?>><?= $u['nama_user'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2"><label>Carta</label>
                            <select name="carta_<?= $e['id_entiti'] ?>" class="form-select">
                                <?php foreach($carta_list as $c): ?>
                                    <option value="<?= $c['id_carta'] ?>" <?= $e['id_carta']==$c['id_carta']?'selected':'' ?>><?= $c['carta'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endforeach; ?>


                <!-- PEGAWAI RUJUKAN -->
                <?php if($peg): ?>
                    <h5 class="mt-4">Pegawai Rujukan</h5>
                    <div class="row">
                        <div class="col-md-3"><label>Nama</label>
                            <select name="nama_userprofile" class="form-select">
                                <?php foreach($userprofiles as $u): ?>
                                    <option value="<?= $u['id_userprofile'] ?>" <?= $peg['id_userprofile']==$u['id_userprofile']?'selected':'' ?>><?= $u['nama_user'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>


                <button type="submit" class="btn add-btn mt-3"><i class="bi bi-check-circle"></i> Simpan</button>
            </form>
        </div>

    </div>

</body>
</html>
