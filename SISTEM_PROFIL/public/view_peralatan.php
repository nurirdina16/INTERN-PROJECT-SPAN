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
            p.id_profilsistem,
            p.nama_entiti,
            p.alamat_pejabat,
            p.tarikh_kemaskini,
            ls.status,
            lb.bahagianunit,

            -- Ketua
            ketua.nama_user AS nama_ketua_nama,
            ketua.jawatan_user AS nama_ketua_jawatan,
            ketua.emel_user AS nama_ketua_emel,
            ketua.notelefon_user AS nama_ketua_notel,

            -- CIO
            cio.nama_user AS nama_cio_nama,
            cio.jawatan_user AS nama_cio_jawatan,
            cio.emel_user AS nama_cio_emel,
            cio.notelefon_user AS nama_cio_notel,

            -- ICTSO
            ictso.nama_user AS nama_ictso_nama,
            ictso.jawatan_user AS nama_ictso_jawatan,
            ictso.emel_user AS nama_ictso_emel,
            ictso.notelefon_user AS nama_ictso_notel,

            lc.carta,

            -- PERALATAN DETAILS
            pr.nama_peralatan,
            ljp.jenis_peralatan,
            pr.siri_peralatan,
            pr.lokasi_peralatan,
            pr.jenama_model,
            pr.tarikh_dibeli,
            pr.tempoh_jaminan_peralatan,
            pr.expired_jaminan,
            lp.penyelenggaraan,
            pr.kos_penyelenggaraan_tahunan,
            pr.tarikh_penyelenggaraan_terakhir,

            -- PEMBEKAL
            pemb.nama_syarikat,
            pemb.alamat_syarikat,
            pemb.tempoh_kontrak,

            -- PIC PEMBEKAL
            pic.nama_PIC AS pic_nama,
            pic.emel_PIC AS pic_emel,
            pic.notelefon_PIC AS pic_tel,
            pic.fax_PIC AS pic_fax,
            pic.jawatan_PIC AS pic_jawatan,

            -- PEGAWAI RUJUKAN PERALATAN
            ref.nama_user AS pegawai_rujukan_nama,
            ref.jawatan_user AS pegawai_rujukan_jawatan,
            ref.emel_user AS pegawai_rujukan_emel,
            ref.notelefon_user AS pegawai_rujukan_notel

        FROM PROFIL p
        INNER JOIN PERALATAN pr 
            ON p.id_profilsistem = pr.id_profilsistem

        LEFT JOIN lookup_status ls ON p.id_status = ls.id_status
        LEFT JOIN lookup_bahagianunit lb ON p.id_bahagianunit = lb.id_bahagianunit
        LEFT JOIN lookup_carta lc ON p.id_carta = lc.id_carta

        LEFT JOIN lookup_jenisperalatan ljp ON pr.id_jenisperalatan = ljp.id_jenisperalatan
        LEFT JOIN lookup_penyelenggaraan lp ON pr.id_penyelenggaraan = lp.id_penyelenggaraan

        LEFT JOIN lookup_pembekal pemb ON pr.id_pembekal = pemb.id_pembekal
        LEFT JOIN lookup_PIC pic ON pemb.id_PIC = pic.id_PIC

        LEFT JOIN lookup_userprofile ketua ON p.nama_ketua = ketua.id_userprofile
        LEFT JOIN lookup_userprofile cio ON p.nama_cio = cio.id_userprofile
        LEFT JOIN lookup_userprofile ictso ON p.nama_ictso = ictso.id_userprofile

        -- Pegawai rujukan peralatan
        LEFT JOIN lookup_userprofile ref ON pr.pegawai_rujukan_peralatan = ref.id_userprofile

        WHERE p.id_profilsistem = :id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("<div class='alert alert-danger'>Ralat: Peralatan tidak ditemui.</div>");
    }

} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Ralat DB: " . $e->getMessage() . "</div>");
}
?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>View Peralatan | Sistem Profil</title>

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
                    <span><?= htmlspecialchars($data['nama_peralatan']); ?></span>

                    <span class="status-tag ms-auto" style="background:#0077A8;">
                        <?= htmlspecialchars($data['status']); ?>
                    </span>

                    <a href="kemaskini_peralatan.php?id=<?= $data['id_profilsistem']; ?>"   
                    class="btn btn-warning btn-sm ms-3">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
            </div>

            <!-- MAKLUMAT PERALATAN -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT PERALATAN</div>

                <div class="info-row">
                    <div class="info-label">Nama Peralatan</div>
                    <div class="info-value"><?= $data['nama_peralatan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Jenis Peralatan</div>
                    <div class="info-value"><?= $data['jenis_peralatan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Nombor Siri / ID Peralatan</div>
                    <div class="info-value"><?= $data['siri_peralatan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Lokasi Peralatan</div>
                    <div class="info-value"><?= $data['lokasi_peralatan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Jenama / Model</div>
                    <div class="info-value"><?= $data['jenama_model']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Dibeli</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($data['tarikh_dibeli'])); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tempoh Jaminan (Warranty)</div>
                    <div class="info-value"><?= $data['tempoh_jaminan_peralatan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Luput Jaminan (Warranty)</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($data['expired_jaminan'])); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Kaedah Penyelenggaraan</div>
                    <div class="info-value"><?= $data['penyelenggaraan']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Kos Penyelenggaraan Tahunan</div>
                    <div class="info-value">RM <?= number_format($data['kos_penyelenggaraan_tahunan'], 2); ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Penyelenggaraan Terakhir</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($data['tarikh_penyelenggaraan_terakhir'])); ?></div>
                </div>
            </div>

            <!-- MAKLUMAT PEMBEKAL -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT PEMBEKAL</div>

                <div class="info-row">
                    <div class="info-label">Nama Syarikat</div>
                    <div class="info-value"><?= $data['nama_syarikat']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Alamat Syarikat</div>
                    <div class="info-value"><?= $data['alamat_syarikat']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tempoh Kontrak</div>
                    <div class="info-value"><?= $data['tempoh_kontrak']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Maklumat PIC</div>                
                    <div class="info-value d-flex flex-column">
                        <span><?= $data['pic_nama']; ?></span>
                        <span class="text-muted"><?= $data['pic_jawatan']; ?></span>
                        <span class="text-muted"><?= $data['pic_emel']; ?></span>
                        <span class="text-muted"><?= $data['pic_tel']; ?></span>
                    </div>
                </div>
            </div>

            <!-- PEGAWAI RUJUKAN -->
            <div class="view-section-box">
                <div class="view-section-title">PEGAWAI RUJUKAN PERALATAN</div>

                <div class="info-row">
                    <div class="info-label">Maklumat Pegawai Rujukan</div>                
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
