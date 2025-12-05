<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// FETCH LOOKUP DATA
$kaedah = $pdo->query("SELECT * FROM lookup_kaedahpembangunan ORDER BY kaedahPembangunan")->fetchAll(PDO::FETCH_ASSOC);
$pemilik = $pdo->query("SELECT * FROM lookup_bahagianunit ORDER BY bahagianunit")->fetchAll(PDO::FETCH_ASSOC);
$jenisprofil = $pdo->query("
    SELECT *
    FROM lookup_jenisprofil
    WHERE id_jenisprofil NOT IN (1, 2)
    ORDER BY jenisprofil
")->fetchAll(PDO::FETCH_ASSOC);

// BUILD FILTERS
$where = [];
$params = [];

if (!empty($_GET['tarikh_mula'])) {
    $where[] = "p.tarikh_mula = :tarikh_mula";
    $params[':tarikh_mula'] = $_GET['tarikh_mula'];
}

if (!empty($_GET['tarikh_siap'])) {
    $where[] = "p.tarikh_siap = :tarikh_siap";
    $params[':tarikh_siap'] = $_GET['tarikh_siap'];
}

if (!empty($_GET['tarikh_guna'])) {
    $where[] = "p.tarikh_guna = :tarikh_guna";
    $params[':tarikh_guna'] = $_GET['tarikh_guna'];
}

if (!empty($_GET['id_kaedahPembangunan'])) {
    $where[] = "p.id_kaedahPembangunan = :id_kaedah";
    $params[':id_kaedah'] = $_GET['id_kaedahPembangunan'];
}

if (!empty($_GET['id_pemilik_profil'])) {
    $where[] = "p.id_pemilik_profil = :pemilik";
    $params[':pemilik'] = $_GET['id_pemilik_profil'];
}

if (!empty($_GET['id_jenisprofil'])) {
    $where[] = "p.id_jenisprofil = :jenisprofil";
    $params[':jenisprofil'] = $_GET['id_jenisprofil'];
}

// SEARCH BAR
$searchQuery = "";
if (!empty($_GET['q'])) {
    $where[] = "(p.nama_profil LIKE :search OR p.nama_entiti LIKE :search)";
    $params[':search'] = "%" . $_GET['q'] . "%";
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// GET ALL PROFIL RESULTS
$sql = "
    SELECT p.*, 
        k.kaedahPembangunan,
        b1.bahagianunit AS pemilik_unit,
        b2.bahagianunit AS pengurus_akses_unit,
        b3.bahagianunit AS inhouse_unit,
        b4.bahagianunit AS bahagian_unit,
        j.jenisprofil,
        s.status,
        kat.kategori,
        peny.penyelenggaraan,
        ku.jenis_dalaman,
        ku.jenis_umum,
        per.jenis_peralatan,
        c.carta,
        up1.nama_user AS pegawai_rujukan_nama,
        up2.nama_user AS ketua_nama,
        up3.nama_user AS cio_nama,
        up4.nama_user AS ictso_nama,
        pb.nama_syarikat AS nama_pembekal
    FROM profil p
    LEFT JOIN lookup_kaedahpembangunan k ON p.id_kaedahPembangunan = k.id_kaedahPembangunan
    LEFT JOIN lookup_bahagianunit b1 ON p.id_pemilik_profil = b1.id_bahagianunit
    LEFT JOIN lookup_bahagianunit b2 ON p.pengurus_akses = b2.id_bahagianunit
    LEFT JOIN lookup_bahagianunit b3 ON p.inhouse = b3.id_bahagianunit
    LEFT JOIN lookup_bahagianunit b4 ON p.id_bahagianunit = b4.id_bahagianunit
    LEFT JOIN lookup_jenisprofil j ON p.id_jenisprofil = j.id_jenisprofil
    LEFT JOIN lookup_status s ON p.id_status = s.id_status
    LEFT JOIN lookup_kategori kat ON p.id_kategori = kat.id_kategori
    LEFT JOIN lookup_penyelenggaraan peny ON p.id_penyelenggaraan = peny.id_penyelenggaraan
    LEFT JOIN lookup_kategoriuser ku ON p.id_kategoriuser = ku.id_kategoriuser
    LEFT JOIN lookup_jenisperalatan per ON p.id_jenisperalatan = per.id_jenisperalatan
    LEFT JOIN lookup_carta c ON p.id_carta = c.id_carta
    LEFT JOIN lookup_pembekal pb ON p.id_pembekal = pb.id_pembekal
    LEFT JOIN lookup_userprofile up1 ON p.pegawai_rujukan = up1.id_userprofile
    LEFT JOIN lookup_userprofile up2 ON p.nama_ketua = up2.id_userprofile
    LEFT JOIN lookup_userprofile up3 ON p.nama_cio = up3.id_userprofile
    LEFT JOIN lookup_userprofile up4 ON p.nama_ictso = up4.id_userprofile
    $where_sql
    ORDER BY p.id_profil DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// FUNCTION MINIMIZE
function minimizeText($text, $limit = 80) {
    $text = trim($text);
    if (strlen($text) <= $limit) {
        return htmlspecialchars($text);
    }

    $short = substr($text, 0, $limit);
    $full = htmlspecialchars($text);

    return <<<HTML
    <span class="short-text">{$short}...</span>
    <span class="full-text d-none">{$full}</span>
    <a href="#" class="toggle-more text-primary" style="font-size:13px;">More</a>
    HTML;
}

?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Maklumat Profil | Sistem Profil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/laporan.css">

    <script src="js/sidebar.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- FIXED HEADER -->
        <?php include 'header.php'; ?>
            
        <div class="main-header mt-3 mb-3"><i class="bi bi-file-earmark-bar-graph"></i> Maklumat Profil</div>

        <div class="profil-card shadow-sm p-4">
            <!-- FILTER -->
            <form method="get" class="mb-4">

                <!-- ROW 1: TARIKH -->
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Tarikh Mula</label>
                        <input type="date" name="tarikh_mula" class="form-control"
                            value="<?= $_GET['tarikh_mula'] ?? '' ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tarikh Siap</label>
                        <input type="date" name="tarikh_siap" class="form-control"
                            value="<?= $_GET['tarikh_siap'] ?? '' ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tarikh Guna</label>
                        <input type="date" name="tarikh_guna" class="form-control"
                            value="<?= $_GET['tarikh_guna'] ?? '' ?>">
                    </div>
                </div>

                <!-- ROW 2: DROPDOWN -->
                <div class="row g-3 align-items-end position-relative">

                    <div class="col-md-3">
                        <label class="form-label">Kaedah Pembangunan</label>
                        <select name="id_kaedahPembangunan" class="form-select">
                            <option value="">Semua</option>
                            <?php foreach ($kaedah as $k): ?>
                                <option value="<?= $k['id_kaedahPembangunan'] ?>"
                                    <?= (($_GET['id_kaedahPembangunan'] ?? '') == $k['id_kaedahPembangunan']) ? 'selected' : '' ?>>
                                    <?= $k['kaedahPembangunan'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Pemilik Profil</label>
                        <select name="id_pemilik_profil" class="form-select">
                            <option value="">Semua</option>
                            <?php foreach ($pemilik as $p): ?>
                                <option value="<?= $p['id_bahagianunit'] ?>"
                                    <?= (($_GET['id_pemilik_profil'] ?? '') == $p['id_bahagianunit']) ? 'selected' : '' ?>>
                                    <?= $p['bahagianunit'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Jenis Profil</label>
                        <select name="id_jenisprofil" class="form-select">
                            <option value="">Semua</option>
                            <?php foreach ($jenisprofil as $j): ?>
                                <option value="<?= $j['id_jenisprofil'] ?>"
                                    <?= (($_GET['id_jenisprofil'] ?? '') == $j['id_jenisprofil']) ? 'selected' : '' ?>>
                                    <?= $j['jenisprofil'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- BUTTONS -->
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-funnel"></i> Tapis
                        </button>
                        <a href="laporan_maklumat.php" class="btn btn-secondary btn-sm flex-fill">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <hr class="mb-4">

            <!-- SEARCH BAR -->
            <form method="get" class="mb-4 mt-2">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Cari nama profil atau entiti..."
                        value="<?= $_GET['q'] ?? '' ?>">
                    <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>


            <!-- TABLE -->
            <div class="table-responsive mt-4 shadow-sm">
                <table class="table table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Status</th>
                            <th>Jenis Profil</th>
                            <th>Nama Profil</th>
                            <th>Nama Entiti</th>
                            <th>Objektif</th>
                            <th>Pemilik Profil</th>
                            <th>Tarikh Mula</th>
                            <th>Tarikh Siap</th>
                            <th>Tarikh Guna</th>
                            <th>Bil Pengguna</th>
                            <th>Bil Modul</th>
                            <th>Kategori</th>
                            <th>Bahasa Pengaturcaraan</th>
                            <th>Pangkalan Data</th>
                            <th>Rangkaian</th>
                            <th>Integrasi</th>
                            <th>Tarikh Dibeli</th>
                            <th>Tempoh Warranty</th>
                            <th>Expired Warranty</th>
                            <th>Kos Perkakasan</th>
                            <th>Kos Perisian</th>
                            <th>Kos Lesen</th>
                            <th>Kos Penyelenggaraan</th>
                            <th>Kos Lain</th>
                            <th>Description Kos</th>
                            <th>Kos Keseluruhan</th>
                            <th>Jenis Peralatan</th>
                            <th>Lokasi</th>
                            <th>No Siri</th>
                            <th>Jenama Model</th>
                            <th>Kategori User Dalaman</th>
                            <th>Kategori User Umum</th>
                            <th>Pengurus Akses</th>
                            <th>Kaedah Pembangunan</th>
                            <th>Pembekal</th>
                            <th>Inhouse</th>
                            <th>Penyelenggaraan</th>
                            <th>Tarikh Akhir Penyelenggaraan</th>
                            <th>Pegawai Rujukan</th>
                            <th>Alamat Pejabat</th>
                            <th>Bahagian Unit</th>
                            <th>Nama Ketua</th>
                            <th>Nama CIO</th>
                            <th>Nama ICTSO</th>
                            <th>Carta</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr><td colspan="9" class="text-center text-muted">Tiada rekod ditemui.</td></tr>
                        <?php else: ?>
                            <?php foreach ($results as $r): ?>
                                <tr>
                                    <td><?= $r['status'] ?></td>
                                    <td><?= $r['jenisprofil'] ?></td>
                                    <td><?= $r['nama_profil'] ?></td>
                                    <td><?= $r['nama_entiti'] ?></td>
                                    <td><?= minimizeText($r['objektif_profil'], 50) ?></td>
                                    <td><?= $r['pemilik_unit'] ?></td>
                                    <td><?= $r['tarikh_mula'] ?></td>
                                    <td><?= $r['tarikh_siap'] ?></td>
                                    <td><?= $r['tarikh_guna'] ?></td>
                                    <td><?= $r['bil_pengguna'] ?></td>
                                    <td><?= $r['bil_modul'] ?></td>
                                    <td><?= $r['kategori'] ?></td>
                                    <td><?= minimizeText($r['bahasa_pengaturcaraan'], 50) ?></td>
                                    <td><?= minimizeText($r['pangkalan_data'], 50) ?></td>
                                    <td><?= minimizeText($r['rangkaian'], 50) ?></td>
                                    <td><?= minimizeText($r['integrasi'], 50) ?></td>
                                    <td><?= $r['tarikh_dibeli'] ?></td>
                                    <td><?= $r['tempoh_warranty'] ?></td>
                                    <td><?= $r['expired_warranty'] ?></td>
                                    <td><?= $r['kos_perkakasan'] ?></td>
                                    <td><?= $r['kos_perisian'] ?></td>
                                    <td><?= $r['kos_lesen_perisian'] ?></td>
                                    <td><?= $r['kos_penyelenggaraan'] ?></td>
                                    <td><?= $r['kos_lain'] ?></td>
                                    <td><?= $r['description_kos'] ?></td>
                                    <td><?= $r['kos_keseluruhan'] ?></td>
                                    <td><?= $r['jenis_peralatan'] ?></td>
                                    <td><?= $r['lokasi'] ?></td>
                                    <td><?= $r['no_siri'] ?></td>
                                    <td><?= $r['jenama_model'] ?></td>
                                    <td><?= $r['jenis_dalaman'] ? 'YA' : 'TIDAK' ?></td>
                                    <td><?= $r['jenis_umum'] ? 'YA' : 'TIDAK' ?></td>
                                    <td><?= $r['pengurus_akses_unit'] ?></td>
                                    <td><?= $r['kaedahPembangunan'] ?></td>
                                    <td><?= $r['nama_pembekal'] ?></td>
                                    <td><?= $r['inhouse_unit'] ?></td>
                                    <td><?= $r['penyelenggaraan'] ?></td>
                                    <td><?= $r['tarikh_akhir_penyelenggaraan'] ?></td>
                                    <td><?= $r['pegawai_rujukan_nama'] ?></td>
                                    <td><?= minimizeText($r['alamat_pejabat'], 50) ?></td>
                                    <td><?= $r['bahagian_unit'] ?></td>
                                    <td><?= $r['ketua_nama'] ?></td>
                                    <td><?= $r['cio_nama'] ?></td>
                                    <td><?= $r['ictso_nama'] ?></td>
                                    <td><?= $r['carta'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("toggle-more")) {
                e.preventDefault();

                let td = e.target.closest("td");
                let shortText = td.querySelector(".short-text");
                let fullText = td.querySelector(".full-text");

                if (fullText.classList.contains("d-none")) {
                    fullText.classList.remove("d-none");
                    shortText.classList.add("d-none");
                    e.target.textContent = "Less";
                } else {
                    fullText.classList.add("d-none");
                    shortText.classList.remove("d-none");
                    e.target.textContent = "More";
                }
            }
        });
    </script>

</body>
</html>
