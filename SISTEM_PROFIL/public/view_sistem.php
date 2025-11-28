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
            LP.alamat_syarikat,
            LP.tempoh_kontrak,
            LPN.penyelenggaraan,
            LKU.jenis_dalaman,
            LKU.jenis_umum,
            LC.carta,
            LB3.bahagianunit AS pengurus_akses_sistem,
            LPP.nama_PIC AS nama_pic,
            LPP.jawatan_PIC AS jawatan_pic,
            LPP.emel_PIC AS emel_pic,
            LPP.notelefon_PIC AS notelefon_pic,

            UP4.jawatan_user AS pegawai_rujukan_jawatan,
            UP4.emel_user AS pegawai_rujukan_emel,
            UP4.notelefon_user AS pegawai_rujukan_notel,

            UP1.jawatan_user AS nama_ketua_jawatan,
            UP1.emel_user AS nama_ketua_emel,
            UP1.notelefon_user AS nama_ketua_notel,

            UP2.jawatan_user AS nama_cio_jawatan,
            UP2.emel_user AS nama_cio_emel,
            UP2.notelefon_user AS nama_cio_notel,

            UP3.jawatan_user AS nama_ictso_jawatan,
            UP3.emel_user AS nama_ictso_emel,
            UP3.notelefon_user AS nama_ictso_notel,

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
        LEFT JOIN LOOKUP_BAHAGIANUNIT LB3 ON S.pengurus_akses = LB3.id_bahagianunit
        LEFT JOIN LOOKUP_KATEGORI LK ON S.id_kategori = LK.id_kategori
        LEFT JOIN LOOKUP_KAEDAHPEMBANGUNAN LKP ON S.id_kaedahpembangunan = LKP.id_kaedahpembangunan
        LEFT JOIN LOOKUP_PEMBEKAL LP ON S.id_pembekal = LP.id_pembekal
        LEFT JOIN LOOKUP_PIC LPP ON LP.id_PIC = LPP.id_PIC
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

                    <span class="status-tag ms-auto" style="background:#0077A8;">
                        <?= htmlspecialchars($data['status']); ?>
                    </span>

                    <a href="kemaskini_sistem.php?id=<?= $data['id_profilsistem']; ?>" 
                    class="btn btn-warning btn-sm ms-3">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
            </div>

            <!-- MAKLUMAT SISTEM -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT SISTEM</div>

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
                    <div class="info-label">Kategori Sistem</div>
                    <div class="info-value"><?= $data['kategori']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Mula Pembangunan Sistem</div>
                    <div class="info-value"><?= $data['tarikh_mula']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Siap Pembangunan Sistem</div>
                    <div class="info-value"><?= $data['tarikh_siap']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Guna Pembangunan Sistem</div>
                    <div class="info-value"><?= $data['tarikh_guna']; ?></div>
                </div>
            </div>

            <!-- INFRASTRUKTUR -->
            <div class="view-section-box">
                <div class="view-section-title">INFRASTRUKTUR SISTEM</div>

                <div class="info-row">
                    <div class="info-label">Bilangan Modul Sistem</div>
                    <div class="info-value"><?= $data['bil_modul']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Bahasa Pengaturcaraan</div>
                    <div class="info-value"><?= $data['bahasa_pengaturcaraan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Jenis Pangkalan Data</div>
                    <div class="info-value"><?= $data['pangkalan_data']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Rangkaian Digunakan</div>
                    <div class="info-value"><?= $data['rangkaian']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Integrasi Sistem Lain</div>
                    <div class="info-value"><?= $data['integrasi']; ?></div>
                </div>
            </div>

            <!-- PEMBANGUNAN -->
            <div class="view-section-box">
                <div class="view-section-title">PEMBANGUNAN SISTEM</div>

                <div class="info-row">
                    <div class="info-label">Kaedah Pembangunan</div>
                    <div class="info-value"><?= $data['kaedahPembangunan']; ?></div>
                </div>

                <hr>

                <div class="view-section-title mb-2" style="font-size:110%; border-bottom:white">Maklumat Dalaman:</div>
                <div class="info-row">
                    <div class="info-label">Bahagian Bertanggungjawab</div>
                    <div class="info-value"><?= $data['inhouse']; ?></div>
                </div>

                <div class="view-section-title mb-2 mt-4" style="font-size:110%; border-bottom:white">Maklumat Pembekal:</div>
                <div class="info-row">
                    <div class="info-label">Nama Syarikat</div>
                    <div class="info-value"><?= $data['nama_syarikat']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Alamat Syarikat</div>
                    <div class="info-value"><?= $data['alamat_syarikat']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tempoh Kontrak (Tahun)</div>
                    <div class="info-value"><?= $data['tempoh_kontrak']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Maklumat PIC</div>                
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['nama_pic']; ?></span>
                        <span class="text-muted"><?= $data['jawatan_pic']; ?></span>
                        <span class="text-muted"><?= $data['emel_pic']; ?></span>
                        <span class="text-muted"><?= $data['notelefon_pic']; ?></span>
                    </div>
                </div>

                <hr>

                <div class="info-row">
                    <div class="info-label">Penyelenggaraan Sistem</div>
                    <div class="info-value"><?= $data['penyelenggaraan']; ?></div>
                </div>
            </div>

            <!-- KEWANGAN -->
            <div class="view-section-box">
                <div class="view-section-title">KOS PEMBANGUNAN SISTEM</div>

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
                    <div class="info-label">Lain-lain Kos</div>
                    <div class="info-value">RM <?= number_format($data['kos_lain'],2); ?></div>
                </div>
            </div>

            <!-- RUJUKAN USER & ACCESS -->
            <div class="view-section-box">
                <div class="view-section-title">RUJUKAN PENGGUNA & AKSES SISTEM</div>

                <div class="info-row">
                    <div class="info-label">Kategori Jenis Pengguna</div>
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
                    <div class="info-label">Pengurus Akses Pengguna</div>
                    <div class="info-value"><?= $data['pengurus_akses_sistem']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Pegawai Rujukan Sistem</div>
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['pegawai_rujukan_nama']; ?></span>
                        <span class="text-muted"><?= $data['pegawai_rujukan_jawatan']; ?></span>
                        <span class="text-muted"><?= $data['pegawai_rujukan_emel']; ?></span>
                        <span class="text-muted"><?= $data['pegawai_rujukan_notel']; ?></span>
                    </div>
                </div>

            </div>

            <!-- MAKLUMAT PROFIL -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT ENTITI</div>

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
                    <div class="info-label">Ketua Bahagian</div>
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['nama_ketua_nama']; ?></span>
                        <span class="text-muted"><?= $data['nama_ketua_jawatan']; ?></span>
                        <span class="text-muted"><?= $data['nama_ketua_emel']; ?></span>
                        <span class="text-muted"><?= $data['nama_ketua_notel']; ?></span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Chief Information Officer (CIO)</div>
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['nama_cio_nama']; ?></span>
                        <span class="text-muted"><?= $data['nama_cio_jawatan']; ?></span>
                        <span class="text-muted"><?= $data['nama_cio_emel']; ?></span>
                        <span class="text-muted"><?= $data['nama_cio_notel']; ?></span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Chief Security Officer (ICTSO)</div>
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['nama_ictso_nama']; ?></span>
                        <span class="text-muted"><?= $data['nama_ictso_jawatan']; ?></span>
                        <span class="text-muted"><?= $data['nama_ictso_emel']; ?></span>
                        <span class="text-muted"><?= $data['nama_ictso_notel']; ?></span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Carta Organisasi</div>
                    <div class="info-value"><?= $data['carta']; ?></div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
