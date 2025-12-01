<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    die("Ralat: ID Profil tidak diberikan.");
}

$id = intval($_GET['id']);

// FETCH PROFIL + ALL LOOKUP TABLES
$sql = "
    SELECT P.*,

    -- LOOKUP FIELDS
    S.status,
    JP.jenisprofil,
    B1.bahagianunit AS pemilik_profil,
    K.kategori,
    JPE.jenis_peralatan,
    KU.jenis_dalaman, KU.jenis_umum,
    KP.kaedahPembangunan,
    PB.nama_syarikat, PB.alamat_syarikat, PB.tempoh_kontrak,
    PIC.nama_PIC, PIC.jawatan_PIC, PIC.emel_PIC, PIC.notelefon_PIC,
    INH.bahagianunit AS inhouse_unit,
    PA.bahagianunit AS pengurus_akses_unit,

    PEN.penyelenggaraan,
    C.carta,

    BU.bahagianunit AS unit_entiti,
    KETUA.nama_user AS nama_ketua,
    KETUA.jawatan_user AS nama_ketua_jawatan,
    KETUA.emel_user AS nama_ketua_emel,
    KETUA.notelefon_user AS nama_ketua_notel,
    
    CIO.nama_user AS nama_cio,
    CIO.jawatan_user AS nama_cio_jawatan,
    CIO.emel_user AS nama_cio_emel,
    CIO.notelefon_user AS nama_cio_notel,

    ICTSO.nama_user AS nama_ictso,
    ICTSO.jawatan_user AS nama_ictso_jawatan,
    ICTSO.emel_user AS nama_ictso_emel,
    ICTSO.notelefon_user AS nama_ictso_notel,

    RUJ.nama_user AS pegawai_rujukan_nama,
    RUJ.jawatan_user AS pegawai_rujukan_jawatan,
    RUJ.emel_user AS pegawai_rujukan_emel,
    RUJ.notelefon_user AS pegawai_rujukan_notel

    FROM profil P
    LEFT JOIN lookup_status S ON P.id_status = S.id_status
    LEFT JOIN lookup_jenisprofil JP ON P.id_jenisprofil = JP.id_jenisprofil
    LEFT JOIN lookup_bahagianunit B1 ON P.id_pemilik_profil = B1.id_bahagianunit
    LEFT JOIN lookup_kategori K ON P.id_kategori = K.id_kategori
    LEFT JOIN lookup_jenisperalatan JPE ON P.id_jenisperalatan = JPE.id_jenisperalatan
    LEFT JOIN lookup_kategoriuser KU ON P.id_kategoriuser = KU.id_kategoriuser
    LEFT JOIN lookup_kaedahpembangunan KP ON P.id_kaedahpembangunan = KP.id_kaedahPembangunan
    LEFT JOIN lookup_pembekal PB ON P.id_pembekal = PB.id_pembekal
    LEFT JOIN lookup_pic PIC ON PB.id_PIC = PIC.id_PIC
    LEFT JOIN lookup_penyelenggaraan PEN ON P.id_penyelenggaraan = PEN.id_penyelenggaraan
    LEFT JOIN lookup_carta C ON P.id_carta = C.id_carta

    LEFT JOIN lookup_bahagianunit BU ON P.id_bahagianunit = BU.id_bahagianunit
    LEFT JOIN lookup_userprofile KETUA ON P.nama_ketua = KETUA.id_userprofile
    LEFT JOIN lookup_userprofile CIO ON P.nama_cio = CIO.id_userprofile
    LEFT JOIN lookup_userprofile ICTSO ON P.nama_ictso = ICTSO.id_userprofile
    LEFT JOIN lookup_userprofile RUJ ON P.pegawai_rujukan = RUJ.id_userprofile
    LEFT JOIN lookup_bahagianunit INH ON P.inhouse = INH.id_bahagianunit
    LEFT JOIN lookup_bahagianunit PA ON P.pengurus_akses = PA.id_bahagianunit

    WHERE P.id_profil = :id
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Ralat: Rekod profil tidak dijumpai!");
}

?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>View Profil | Sistem Profil</title>

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
                    <i class="bi "></i>
                    <span><?= htmlspecialchars($data['nama_profil']); ?></span>

                    <span class="status-tag ms-auto" style="background:#0077A8;">
                        <?= htmlspecialchars($data['status']); ?>
                    </span>

                    <span class="status-tag ms-1" style="background:#0077A8;">
                        <?= htmlspecialchars($data['jenisprofil']); ?>
                    </span>

                    <a href="kemaskini_profil.php?id=<?= $data['id_profil']; ?>"      
                    class="btn btn-warning btn-sm ms-1">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
            </div>

            <!-- MAKLUMAT PROFIL -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT PROFIL</div>

                <div class="info-row">
                    <div class="info-label">Nama Profil</div>
                    <div class="info-value"><?= $data['nama_profil']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Pemilik Profil</div>
                    <div class="info-value"><?= $data['pemilik_profil']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Objektif Profil</div>
                    <div class="info-value objective-box"><?= nl2br($data['objektif_profil']); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Mula Pembangunan</div>
                    <div class="info-value"><?= $data['tarikh_mula']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Siap Pembangunan</div>
                    <div class="info-value"><?= $data['tarikh_siap']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Digunakan</div>
                    <div class="info-value"><?= $data['tarikh_guna']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Dibeli / Diterima</div>
                    <div class="info-value"><?= $data['tarikh_dibeli']; ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Kategori</div>
                    <div class="info-value"><?= $data['kategori']; ?></div>
                </div>
            </div>

            <!-- MAKLUMAT TEKNIKAL -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT TEKNIKAL</div>

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

                <hr>

                <div class="info-row">
                    <div class="info-label">Jenis Peralatan</div>
                    <div class="info-value"><?= $data['jenis_peralatan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Nombor Siri / ID</div>
                    <div class="info-value"><?= $data['no_siri']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Jenama / Model</div>
                    <div class="info-value"><?= $data['jenama_model']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Lokasi (Bangunan, Aras, Unit)</div>
                    <div class="info-value"><?= $data['lokasi']; ?></div>
                </div>

                <hr>

                <div class="info-row">
                    <div class="info-label">Tempoh Jaminan (Tahun)</div>
                    <div class="info-value"><?= $data['tempoh_warranty']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Luput Jaminan</div>
                    <div class="info-value"><?= $data['expired_warranty']; ?></div>
                </div>

            </div>

            <!-- KOS & PENYELENGGARAAN -->
            <div class="view-section-box">
                <div class="view-section-title">KOS & PENYELENGGARAAN</div>

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

                <div class="info-row">
                    <div class="info-label" style="font-weight: 700;">Jumlah Kos</div>
                    <div class="info-value" style="font-weight: 700;">RM <?= number_format($data['kos_keseluruhan'],2); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Keterangan Kos</div>
                    <div class="info-value objective-box"><?= nl2br($data['description_kos']); ?></div>
                </div>

                <hr>

                <div class="info-row">
                    <div class="info-label">Kaedah Penyelenggaraan</div>
                    <div class="info-value"><?= $data['penyelenggaraan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Akhir Penyelenggaraan</div>
                    <div class="info-value"><?= $data['tarikh_akhir_penyelenggaraan']; ?></div>
                </div>
            </div>

            <!-- PEMBANGUNAN -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT PEMBANGUNAN</div>

                <div class="info-row">
                    <div class="info-label">Kaedah Pembangunan</div>
                    <div class="info-value"><?= $data['kaedahPembangunan']; ?></div>
                </div>

                <hr>

                <div class="view-section-title mb-2" style="font-size:110%; border-bottom:white">Maklumat Dalaman:</div>
                <div class="info-row">
                    <div class="info-label">Bahagian Bertanggungjawab</div>
                    <div class="info-value"><?= $data['inhouse_unit']; ?></div>
                </div>

                <hr>

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
                        <span><?= $data['nama_PIC']; ?></span>
                        <span class="text-muted"><?= $data['jawatan_PIC']; ?></span>
                        <span class="text-muted"><?= $data['emel_PIC']; ?></span>
                        <span class="text-muted"><?= $data['notelefon_PIC']; ?></span>
                    </div> 
                </div>
            </div>

            <!-- RUJUKAN USER & ACCESS -->
            <div class="view-section-box">
                <div class="view-section-title">RUJUKAN PENGGUNA & AKSES</div>

                <div class="info-row">
                    <div class="info-label">Pengurus Akses Pengguna</div>
                    <div class="info-value"><?= $data['pengurus_akses_unit']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Pegawai Rujukan</div>
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['pegawai_rujukan_nama']; ?></span>
                        <span class="text-muted"><?= $data['pegawai_rujukan_jawatan']; ?></span>
                        <span class="text-muted"><?= $data['pegawai_rujukan_emel']; ?></span>
                        <span class="text-muted"><?= $data['pegawai_rujukan_notel']; ?></span>
                    </div>
                </div>
            </div>

            <!-- MAKLUMAT ENTITI -->
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
                    <div class="info-value"><?= $data['unit_entiti']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Ketua Bahagian</div>
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['nama_ketua']; ?></span>
                        <span class="text-muted"><?= $data['nama_ketua_jawatan']; ?></span>
                        <span class="text-muted"><?= $data['nama_ketua_emel']; ?></span>
                        <span class="text-muted"><?= $data['nama_ketua_notel']; ?></span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Chief Information Officer (CIO)</div>
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['nama_cio']; ?></span>
                        <span class="text-muted"><?= $data['nama_cio_jawatan']; ?></span>
                        <span class="text-muted"><?= $data['nama_cio_emel']; ?></span>
                        <span class="text-muted"><?= $data['nama_cio_notel']; ?></span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Chief Security Officer (ICTSO)</div>
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['nama_ictso']; ?></span>
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
