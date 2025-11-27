<div class="section-title"><i class="bi bi-building"></i> MAKLUMAT PROFIL (Entiti)</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label for="id_status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="id_status" id="id_status" class="form-select" required>
            <option value="">-- Pilih Status --</option>
            <?php foreach ($statuses as $s): ?>
                <option value="<?= $s['id_status'] ?>"><?= $s['status'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-8">
        <label for="nama_entiti" class="form-label">Nama Entiti <span class="text-danger">*</span></label>
        <input type="text" name="nama_entiti" id="nama_entiti" class="form-control" maxlength="100" required>
    </div>
    <div class="col-md-12">
        <label for="alamat_pejabat" class="form-label">Alamat Pejabat</label>
        <textarea name="alamat_pejabat" id="alamat_pejabat" class="form-control" rows="2" maxlength="255"></textarea>
    </div>
    <div class="col-md-4">
        <label for="id_bahagianunit" class="form-label">Bahagian/Unit <span class="text-danger">*</span></label>
        <select name="id_bahagianunit" id="id_bahagianunit" class="form-select" required>
            <option value="">-- Pilih Bahagian/Unit --</option>
            <?php foreach ($bahagianunits as $bu): ?>
                <option value="<?= $bu['id_bahagianunit'] ?>"><?= $bu['bahagianunit'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label for="id_carta" class="form-label">Carta Organisasi</label>
        <select name="id_carta" id="id_carta" class="form-select">
            <option value="">-- Pilih Carta --</option>
            <?php foreach ($cartas as $c): ?>
                <option value="<?= $c['id_carta'] ?>"><?= $c['carta'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="section-title mt-4"><i class="bi bi-person-workspace"></i> PEGAWAI RUJUKAN PROFIL</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label for="nama_ketua" class="form-label">Nama Ketua <span class="text-danger">*</span></label>
        <select name="nama_ketua" id="nama_ketua" class="form-select" required>
            <option value="">-- Pilih Pegawai --</option>
            <?php foreach ($userprofiles as $up): ?>
                <option value="<?= $up['id_userprofile'] ?>"><?= $up['nama_user'] ?> (<?= $up['jawatan_user'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label for="nama_cio" class="form-label">Nama CIO <span class="text-danger">*</span></label>
        <select name="nama_cio" id="nama_cio" class="form-select" required>
            <option value="">-- Pilih Pegawai --</option>
            <?php foreach ($userprofiles as $up): ?>
                <option value="<?= $up['id_userprofile'] ?>"><?= $up['nama_user'] ?> (<?= $up['jawatan_user'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label for="nama_ictso" class="form-label">Nama ICTSO <span class="text-danger">*</span></label>
        <select name="nama_ictso" id="nama_ictso" class="form-select" required>
            <option value="">-- Pilih Pegawai --</option>
            <?php foreach ($userprofiles as $up): ?>
                <option value="<?= $up['id_userprofile'] ?>"><?= $up['nama_user'] ?> (<?= $up['jawatan_user'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

---

<div class="section-title"><i class="bi bi-gear"></i> MAKLUMAT SISTEM</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="nama_sistem" class="form-label">Nama Sistem <span class="text-danger">*</span></label>
        <input type="text" name="nama_sistem" id="nama_sistem" class="form-control" maxlength="100" required>
    </div>
    <div class="col-md-6">
        <label for="id_pemilik_sistem" class="form-label">Pemilik Sistem (Bahagian/Unit) <span class="text-danger">*</span></label>
        <select name="id_pemilik_sistem" id="id_pemilik_sistem" class="form-select" required>
            <option value="">-- Pilih Bahagian/Unit --</option>
            <?php foreach ($bahagianunits as $bu): ?>
                <option value="<?= $bu['id_bahagianunit'] ?>"><?= $bu['bahagianunit'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-12">
        <label for="objektif_sistem" class="form-label">Objektif Sistem</label>
        <textarea name="objektif_sistem" id="objektif_sistem" class="form-control" rows="3"></textarea>
    </div>
    <div class="col-md-4">
        <label for="tarikh_mula" class="form-label">Tarikh Mula Pembangunan</label>
        <input type="date" name="tarikh_mula" id="tarikh_mula" class="form-control">
    </div>
    <div class="col-md-4">
        <label for="tarikh_siap" class="form-label">Tarikh Siap Pembangunan</label>
        <input type="date" name="tarikh_siap" id="tarikh_siap" class="form-control">
    </div>
    <div class="col-md-4">
        <label for="tarikh_guna" class="form-label">Tarikh Mula Digunakan</label>
        <input type="date" name="tarikh_guna" id="tarikh_guna" class="form-control">
    </div>
    <div class="col-md-4">
        <label for="bil_pengguna" class="form-label">Anggaran Bilangan Pengguna</label>
        <input type="text" name="bil_pengguna" id="bil_pengguna" class="form-control" maxlength="50">
    </div>
    <div class="col-md-4">
        <label for="bil_modul" class="form-label">Anggaran Bilangan Modul</label>
        <input type="text" name="bil_modul" id="bil_modul" class="form-control" maxlength="50">
    </div>
    <div class="col-md-4">
        <label for="id_kategori" class="form-label">Kategori Sistem <span class="text-danger">*</span></label>
        <select name="id_kategori" id="id_kategori" class="form-select" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategoris as $k): ?>
                <option value="<?= $k['id_kategori'] ?>"><?= $k['kategori'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label for="bahasa_pengaturcaraan" class="form-label">Bahasa Pengaturcaraan</label>
        <input type="text" name="bahasa_pengaturcaraan" id="bahasa_pengaturcaraan" class="form-control">
    </div>
    <div class="col-md-4">
        <label for="pangkalan_data" class="form-label">Pangkalan Data (DBMS)</label>
        <input type="text" name="pangkalan_data" id="pangkalan_data" class="form-control">
    </div>
    <div class="col-md-4">
        <label for="rangkaian" class="form-label">Rangkaian (Contoh: LAN, Internet)</label>
        <input type="text" name="rangkaian" id="rangkaian" class="form-control">
    </div>
    <div class="col-md-6">
        <label for="integrasi" class="form-label">Integrasi (Sistem Luaran)</label>
        <input type="text" name="integrasi" id="integrasi" class="form-control">
    </div>
    <div class="col-md-6">
        <label for="id_kaedahpembangunan" class="form-label">Kaedah Pembangunan <span class="text-danger">*</span></label>
        <select name="id_kaedahpembangunan" id="id_kaedahpembangunan" class="form-select" required>
            <option value="">-- Pilih Kaedah --</option>
            <?php foreach ($kaedahPembangunans as $kp): ?>
                <option value="<?= $kp['id_kaedahPembangunan'] ?>"><?= $kp['kaedahPembangunan'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="section-title mt-4"><i class="bi bi-wallet2"></i> MAKLUMAT PEROLEHAN & PENYELENGGARAAN</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="id_pembekal" class="form-label">Pembekal Utama</label>
        <select name="id_pembekal" id="id_pembekal" class="form-select">
            <option value="">-- Pilih Pembekal --</option>
            <?php foreach ($pembekals as $p): ?>
                <option value="<?= $p['id_pembekal'] ?>"><?= $p['nama_syarikat'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="inhouse" class="form-label">Pembangunan Inhouse (Jika Ya, Bahagian/Unit)</label>
        <select name="inhouse" id="inhouse" class="form-select">
            <option value="">-- Pilih Bahagian/Unit --</option>
            <?php foreach ($bahagianunits as $bu): ?>
                <option value="<?= $bu['id_bahagianunit'] ?>"><?= $bu['bahagianunit'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label for="tarikh_dibeli" class="form-label">Tarikh Dibeli/Perjanjian</label>
        <input type="date" name="tarikh_dibeli" id="tarikh_dibeli" class="form-control">
    </div>
    <div class="col-md-4">
        <label for="tempoh_jaminan_sistem" class="form-label">Tempoh Jaminan (Cth: 1 Tahun)</label>
        <input type="text" name="tempoh_jaminan_sistem" id="tempoh_jaminan_sistem" class="form-control" maxlength="50">
    </div>
    <div class="col-md-4">
        <label for="expired_jaminan_sistem" class="form-label">Tarikh Tamat Jaminan</label>
        <input type="date" name="expired_jaminan_sistem" id="expired_jaminan_sistem" class="form-control">
    </div>
    <div class="col-md-6">
        <label for="id_penyelenggaraan" class="form-label">Jenis Penyelenggaraan <span class="text-danger">*</span></label>
        <select name="id_penyelenggaraan" id="id_penyelenggaraan" class="form-select" required>
            <option value="">-- Pilih Jenis Penyelenggaraan --</option>
            <?php foreach ($penyelenggaraans as $py): ?>
                <option value="<?= $py['id_penyelenggaraan'] ?>"><?= $py['penyelenggaraan'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="kos_keseluruhan" class="form-label">Kos Keseluruhan (RM)</label>
        <input type="number" step="0.01" name="kos_keseluruhan" id="kos_keseluruhan" class="form-control" placeholder="0.00">
    </div>
    <div class="col-md-4">
        <label for="kos_perkakasan" class="form-label">Kos Perkakasan (RM)</label>
        <input type="number" step="0.01" name="kos_perkakasan" id="kos_perkakasan" class="form-control" placeholder="0.00">
    </div>
    <div class="col-md-4">
        <label for="kos_perisian" class="form-label">Kos Perisian (RM)</label>
        <input type="number" step="0.01" name="kos_perisian" id="kos_perisian" class="form-control" placeholder="0.00">
    </div>
    <div class="col-md-4">
        <label for="kos_lesen_perisian" class="form-label">Kos Lesen Perisian (RM)</label>
        <input type="number" step="0.01" name="kos_lesen_perisian" id="kos_lesen_perisian" class="form-control" placeholder="0.00">
    </div>
    <div class="col-md-4">
        <label for="kos_penyelenggaraan" class="form-label">Kos Penyelenggaraan (RM)</label>
        <input type="number" step="0.01" name="kos_penyelenggaraan" id="kos_penyelenggaraan" class="form-control" placeholder="0.00">
    </div>
    <div class="col-md-4">
        <label for="kos_lain" class="form-label">Kos Lain-Lain (RM)</label>
        <input type="number" step="0.01" name="kos_lain" id="kos_lain" class="form-control" placeholder="0.00">
    </div>
</div>

<div class="section-title mt-4"><i class="bi bi-people"></i> PENGURUSAN PENGGUNA</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="id_kategoriuser" class="form-label">Kategori Pengguna <span class="text-danger">*</span></label>
        <select name="id_kategoriuser" id="id_kategoriuser" class="form-select" required>
            <option value="">-- Pilih Kategori Pengguna --</option>
            <?php foreach ($kategoriusers as $ku): ?>
                <option value="<?= $ku['id_kategoriuser'] ?>">
                    <?php 
                        $types = [];
                        if ($ku['jenis_dalaman']) $types[] = 'Dalaman';
                        if ($ku['jenis_umum']) $types[] = 'Umum';
                        echo implode(' & ', $types);
                    ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="pengurus_akses" class="form-label">Pengurus Akses Sistem (Bahagian/Unit) <span class="text-danger">*</span></label>
        <select name="pengurus_akses" id="pengurus_akses" class="form-select" required>
            <option value="">-- Pilih Bahagian/Unit --</option>
            <?php foreach ($bahagianunits as $bu): ?>
                <option value="<?= $bu['id_bahagianunit'] ?>"><?= $bu['bahagianunit'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="pegawai_rujukan_sistem" class="form-label">Pegawai Rujukan Sistem <span class="text-danger">*</span></label>
        <select name="pegawai_rujukan_sistem" id="pegawai_rujukan_sistem" class="form-select" required>
            <option value="">-- Pilih Pegawai --</option>
            <?php foreach ($userprofiles as $up): ?>
                <option value="<?= $up['id_userprofile'] ?>"><?= $up['nama_user'] ?> (<?= $up['jawatan_user'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
</div>