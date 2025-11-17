<?php
require_once '../../app/config.php';
require_once '../../app/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_sistemutama = $_POST['id_sistemutama'] ?? null;
        if (!$id_sistemutama) {
            throw new Exception("ID Sistem Utama tidak ditemui.");
        }

        // 1️⃣ SISTEM_UTAMA
        $stmt = $pdo->prepare("
            UPDATE sistem_utama SET
                nama_entiti = :nama_entiti,
                tarikh_kemaskini = :tarikh_kemaskini,
                bahagian = :bahagian,
                alamat = :alamat,
                nama_ketua = :nama_ketua,
                no_telefon = :no_telefon,
                no_faks = :no_faks,
                emel_ketua = :emel_ketua,
                cio = :cio,
                ictso = :ictso,
                carta_organisasi = :carta_organisasi
            WHERE id_sistemutama = :id_sistemutama
        ");
        $stmt->execute([
            ':nama_entiti' => $_POST['nama_entiti'] ?? '',
            ':tarikh_kemaskini' => $_POST['tarikh_kemaskini'] ?? null,
            ':bahagian' => $_POST['bahagian'] ?? '',
            ':alamat' => $_POST['alamat'] ?? '',
            ':nama_ketua' => $_POST['nama_ketua'] ?? '',
            ':no_telefon' => $_POST['no_telefon'] ?? '',
            ':no_faks' => $_POST['no_faks'] ?? '',
            ':emel_ketua' => $_POST['emel_ketua'] ?? '',
            ':cio' => $_POST['cio'] ?? '',
            ':ictso' => $_POST['ictso'] ?? '',
            ':carta_organisasi' => $_POST['carta_organisasi'] ?? '',
            ':id_sistemutama' => $id_sistemutama
        ]);

        // 2️⃣ SISTEM_APLIKASI
        $stmt2 = $pdo->prepare("
            UPDATE sistem_aplikasi SET
                nama_sistem = :nama_sistem,
                objektif = :objektif,
                pemilik = :pemilik,
                tarikh_mula = :tarikh_mula,
                tarikh_siap = :tarikh_siap,
                tarikh_guna = :tarikh_guna,
                bil_pengguna = :bil_pengguna,
                kaedah_pembangunan = :kaedah_pembangunan,
                inhouse = :inhouse,
                outsource = :outsource,
                bil_modul = :bil_modul,
                kategori = :kategori,
                bahasa_pengaturcaraan = :bahasa_pengaturcaraan,
                pangkalan_data = :pangkalan_data,
                rangkaian = :rangkaian,
                integrasi = :integrasi,
                penyelenggaraan = :penyelenggaraan
            WHERE id_sistemutama = :id_sistemutama
        ");
        $stmt2->execute([
            ':nama_sistem' => $_POST['nama_sistem'] ?? '',
            ':objektif' => $_POST['objektif'] ?? '',
            ':pemilik' => $_POST['pemilik'] ?? '',
            ':tarikh_mula' => $_POST['tarikh_mula'] ?? null,
            ':tarikh_siap' => $_POST['tarikh_siap'] ?? null,
            ':tarikh_guna' => $_POST['tarikh_guna'] ?? null,
            ':bil_pengguna' => $_POST['bil_pengguna'] ?? '',
            ':kaedah_pembangunan' => $_POST['kaedah_pembangunan'] ?? '',
            ':inhouse' => $_POST['inhouse'] ?? '',
            ':outsource' => $_POST['outsource'] ?? '',
            ':bil_modul' => $_POST['bil_modul'] ?? '',
            ':kategori' => $_POST['kategori'] ?? '',
            ':bahasa_pengaturcaraan' => $_POST['bahasa_pengaturcaraan'] ?? '',
            ':pangkalan_data' => $_POST['pangkalan_data'] ?? '',
            ':rangkaian' => $_POST['rangkaian'] ?? '',
            ':integrasi' => $_POST['integrasi'] ?? '',
            ':penyelenggaraan' => $_POST['penyelenggaraan'] ?? '',
            ':id_sistemutama' => $id_sistemutama
        ]);

        // 3️⃣ KOS_SISTEM
        $stmt3 = $pdo->prepare("
            UPDATE kos_sistem SET
                keseluruhan = :keseluruhan,
                perkakasan = :perkakasan,
                perisian = :perisian,
                lesen_perisian = :lesen_perisian,
                penyelenggaraan_kos = :penyelenggaraan_kos,
                kos_lain = :kos_lain
            WHERE id_sistemutama = :id_sistemutama
        ");
        $stmt3->execute([
            ':keseluruhan' => $_POST['keseluruhan'] ?? 0,
            ':perkakasan' => $_POST['perkakasan'] ?? 0,
            ':perisian' => $_POST['perisian'] ?? 0,
            ':lesen_perisian' => $_POST['lesen_perisian'] ?? 0,
            ':penyelenggaraan_kos' => $_POST['penyelenggaraan_kos'] ?? 0,
            ':kos_lain' => $_POST['kos_lain'] ?? 0,
            ':id_sistemutama' => $id_sistemutama
        ]);

        // 4️⃣ AKSES_SISTEM
        $stmt4 = $pdo->prepare("
            UPDATE akses_sistem SET
                kategori_dalaman = :kategori_dalaman,
                kategori_umum = :kategori_umum,
                pegawai_urus_akses = :pegawai_urus_akses
            WHERE id_sistemutama = :id_sistemutama
        ");
        $stmt4->execute([
            ':kategori_dalaman' => $_POST['kategori_dalaman'] ?? 0,
            ':kategori_umum' => $_POST['kategori_umum'] ?? 0,
            ':pegawai_urus_akses' => $_POST['pegawai_urus_akses'] ?? '',
            ':id_sistemutama' => $id_sistemutama
        ]);

        // 5️⃣ PEGAWAI_RUJUKAN_SISTEM
        $stmt5 = $pdo->prepare("
            UPDATE pegawai_rujukan_sistem SET
                nama_pegawai = :nama_pegawai,
                jawatan_gred = :jawatan_gred,
                bahagian = :bahagian,
                emel_pegawai = :emel_pegawai,
                no_telefon = :no_telefon
            WHERE id_sistemutama = :id_sistemutama
        ");
        $stmt5->execute([
            ':nama_pegawai' => $_POST['nama_pegawai'] ?? '',
            ':jawatan_gred' => $_POST['jawatan_gred'] ?? '',
            ':bahagian' => $_POST['bahagian_pegawai'] ?? '',
            ':emel_pegawai' => $_POST['emel_pegawai'] ?? '',
            ':no_telefon' => $_POST['no_telefon_pegawai'] ?? '',
            ':id_sistemutama' => $id_sistemutama
        ]);

        // 🔹 Redirect ke view_sistem.php dengan alert
        header("Location: view_sistem.php?id={$id_sistemutama}&updated=1");
        exit;

    } catch (Exception $e) {
        die("❌ Ralat semasa kemaskini: " . $e->getMessage());
    }
} else {
    header("Location: sistemUtama.php");
    exit;
}
