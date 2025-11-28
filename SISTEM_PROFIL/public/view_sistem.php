<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    die("<div class='alert alert-danger'>Ralat: ID sistem tidak diberikan.</div>");
}

$id = $_GET['id'];

try {
    $sql = "
        SELECT 
            P.*, 
            S.*,

            -- LOOKUPS
            LS.status,
            LJ.jenisprofil,
            LB.bahagianunit,
            LB2.bahagianunit AS pemilik_sistem_nama,
            LK.kategori,
            LKP.kaedahPembangunan,
            LP.nama_syarikat,
            LPN.penyelenggaraan,
            LKU.jenis_dalaman,
            LKU.jenis_umum,
            LC.carta,

            -- USERPROFILE LINKED FIELDS
            UP1.nama_user AS nama_ketua_nama,
            UP2.nama_user AS nama_cio_nama,
            UP3.nama_user AS nama_ictso_nama,
            UP4.nama_user AS pegawai_rujukan_nama

        FROM PROFIL P
        INNER JOIN SISTEM S ON P.id_profilsistem = S.id_profilsistem

        -- LOOKUP JOINS
        LEFT JOIN LOOKUP_STATUS LS ON P.id_status = LS.id_status
        LEFT JOIN LOOKUP_JENISPROFIL LJ ON P.id_jenisprofil = LJ.id_jenisprofil
        LEFT JOIN LOOKUP_BAHAGIANUNIT LB ON P.id_bahagianunit = LB.id_bahagianunit
        LEFT JOIN LOOKUP_BAHAGIANUNIT LB2 ON S.id_pemilik_sistem = LB2.id_bahagianunit
        LEFT JOIN LOOKUP_KATEGORI LK ON S.id_kategori = LK.id_kategori
        LEFT JOIN LOOKUP_KAEDAHPEMBANGUNAN LKP ON S.id_kaedahpembangunan = LKP.id_kaedahpembangunan
        LEFT JOIN LOOKUP_PEMBEKAL LP ON S.id_pembekal = LP.id_pembekal
        LEFT JOIN LOOKUP_PENYELENGGARAAN LPN ON S.id_penyelenggaraan = LPN.id_penyelenggaraan
        LEFT JOIN LOOKUP_KATEGORIUSER LKU ON S.id_kategoriuser = LKU.id_kategoriuser
        LEFT JOIN LOOKUP_CARTA LC ON P.id_carta = LC.id_carta

        -- USER PROFILES
        LEFT JOIN LOOKUP_USERPROFILE UP1 ON P.nama_ketua = UP1.id_userprofile
        LEFT JOIN LOOKUP_USERPROFILE UP2 ON P.nama_cio = UP2.id_userprofile
        LEFT JOIN LOOKUP_USERPROFILE UP3 ON P.nama_ictso = UP3.id_userprofile
        LEFT JOIN LOOKUP_USERPROFILE UP4 ON S.pegawai_rujukan_sistem = UP4.id_userprofile

        WHERE P.id_profilsistem = :id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("<div class='alert alert-danger'>Ralat: Sistem tidak ditemui.</div>");
    }

} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Ralat DB: " . $e->getMessage() . "</div>");
}
?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>View Sistem | Sistem Profil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>

    <link rel="stylesheet" href="css/profil.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- HEADER -->
        <?php include 'header.php'; ?>

        <div class="profil-card shadow-sm p-4">
            <div class="view-main-header">
                <div class="header-wrapper">
                    <i class="bi bi-pc-display"></i>
                    <span><?= htmlspecialchars($data['nama_sistem']); ?></span>

                    <span class="status-tag ms-3"
                        style="background:#0077A8;">
                        <?= htmlspecialchars($data['status']); ?>
                    </span>
                </div>
            </div>

            <!-- A. MAKLUMAT PROFIL -->
            <div class="view-section-box">
                <div class="view-section-title">A. Maklumat Profil</div>

                <div class="info-row">
                    <div class="info-label">Nama Entiti</div>
                    <div class="info-value"><?= $data['nama_entiti']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Alamat Pejabat</div>
                    <div class="info-value"><?= $data['alamat_pejabat']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Bahagian / Unit</div>
                    <div class="info-value"><?= $data['bahagianunit']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Carta Organisasi</div>
                    <div class="info-value"><?= $data['carta']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Ketua Jabatan</div>
                    <div class="info-value"><?= $data['nama_ketua_nama']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">CIO</div>
                    <div class="info-value"><?= $data['nama_cio_nama']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">ICTSO</div>
                    <div class="info-value"><?= $data['nama_ictso_nama']; ?></div>
                </div>
            </div>


            <!-- B. MAKLUMAT SISTEM -->
            <div class="view-section-box">
                <div class="view-section-title">B. Maklumat Sistem</div>

                <div class="info-row">
                    <div class="info-label">Nama Sistem</div>
                    <div class="info-value"><?= $data['nama_sistem']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Objektif Sistem</div>
                    <div class="info-value objective-box"><?= nl2br($data['objektif_sistem']); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Pemilik Sistem</div>
                    <div class="info-value"><?= $data['pemilik_sistem_nama']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Mula</div>
                    <div class="info-value"><?= $data['tarikh_mula']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Siap</div>
                    <div class="info-value"><?= $data['tarikh_siap']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Guna</div>
                    <div class="info-value"><?= $data['tarikh_guna']; ?></div>
                </div>
            </div>


            <!-- C. PEMBANGUNAN -->
            <div class="view-section-box">
                <div class="view-section-title">C. Kaedah Pembangunan</div>

                <div class="info-row">
                    <div class="info-label">Kaedah</div>
                    <div class="info-value"><?= $data['kaedahPembangunan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Bahasa Pengaturcaraan</div>
                    <div class="info-value"><?= $data['bahasa_pengaturcaraan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Pangkalan Data</div>
                    <div class="info-value"><?= $data['pangkalan_data']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Integrasi</div>
                    <div class="info-value"><?= $data['integrasi']; ?></div>
                </div>
            </div>


            <!-- D. INFRASTRUKTUR -->
            <div class="view-section-box">
                <div class="view-section-title">D. Infrastruktur</div>

                <div class="info-row">
                    <div class="info-label">Rangkaian</div>
                    <div class="info-value"><?= $data['rangkaian']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">In-House</div>
                    <div class="info-value"><?= $data['inhouse']; ?></div>
                </div>
            </div>


            <!-- E. PEMBEKAL -->
            <div class="view-section-box">
                <div class="view-section-title">E. Maklumat Pembekal</div>

                <div class="info-row">
                    <div class="info-label">Nama Syarikat</div>
                    <div class="info-value"><?= $data['nama_syarikat']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Jenis Penyelenggaraan</div>
                    <div class="info-value"><?= $data['penyelenggaraan']; ?></div>
                </div>
            </div>


            <!-- F. KEWANGAN -->
            <div class="view-section-box">
                <div class="view-section-title">F. Kewangan Sistem</div>

                <div class="info-row">
                    <div class="info-label">Kos Keseluruhan</div>
                    <div class="info-value">RM <?= number_format($data['kos_keseluruhan'],2); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Kos Perkakasan</div>
                    <div class="info-value">RM <?= number_format($data['kos_perkakasan'],2); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Kos Perisian</div>
                    <div class="info-value">RM <?= number_format($data['kos_perisian'],2); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Kos Lesen Perisian</div>
                    <div class="info-value">RM <?= number_format($data['kos_lesen_perisian'],2); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Kos Penyelenggaraan</div>
                    <div class="info-value">RM <?= number_format($data['kos_penyelenggaraan'],2); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Kos Lain</div>
                    <div class="info-value">RM <?= number_format($data['kos_lain'],2); ?></div>
                </div>
            </div>


            <!-- G. USER & ACCESS -->
            <div class="view-section-box">
                <div class="view-section-title">G. Pengguna & Akses</div>

                <div class="info-row">
                    <div class="info-label">Kategori User</div>
                    <div class="info-value">
                        <?php if ($data['jenis_dalaman']) echo "<span class='color-tag'>Dalaman</span>"; ?>
                        <?php if ($data['jenis_umum']) echo "<span class='color-tag'>Umum</span>"; ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Bilangan Pengguna</div>
                    <div class="info-value"><?= $data['bil_pengguna']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Bilangan Modul</div>
                    <div class="info-value"><?= $data['bil_modul']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Pegawai Rujukan Sistem</div>
                    <div class="info-value"><?= $data['pegawai_rujukan_nama']; ?></div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
