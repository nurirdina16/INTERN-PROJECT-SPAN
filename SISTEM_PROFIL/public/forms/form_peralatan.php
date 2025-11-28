<input type="hidden" name="id_jenisprofil" value="2">
<div class="section-title"><i class="bi bi-building"></i> MAKLUMAT ENTITI (Jadual PROFIL)</div>
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
    <div class="col-md-6">
        <label for="id_bahagianunit" class="form-label">Bahagian/Unit <span class="text-danger">*</span></label>
        <select name="id_bahagianunit" id="id_bahagianunit" class="form-select" required>
            <option value="">-- Pilih Bahagian/Unit --</option>
            <?php foreach ($bahagianunits as $bu): ?>
                <option value="<?= $bu['id_bahagianunit'] ?>"><?= $bu['bahagianunit'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="id_carta" class="form-label">Carta Organisasi</label>
        <select name="id_carta" id="id_carta" class="form-select">
            <option value="">-- Pilih Carta --</option>
            <?php foreach ($cartas as $c): ?>
                <option value="<?= $c['id_carta'] ?>"><?= $c['carta'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
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

<hr>

<div class="section-title"><i class="bi bi-hdd"></i> MAKLUMAT PERALATAN (Jadual PERALATAN)</div>
<div class="row g-3">
    <div class="col-md-6">
        <label for="nama_peralatan" class="form-label">Nama Peralatan <span class="text-danger">*</span></label>
        <input type="text" name="nama_peralatan" id="nama_peralatan" class="form-control" maxlength="100" required>
    </div>
    <div class="col-md-6">
        <label for="id_jenisperalatan" class="form-label">Jenis Peralatan <span class="text-danger">*</span></label>
        <select name="id_jenisperalatan" id="id_jenisperalatan" class="form-select" required>
            <option value="">-- Pilih Jenis --</option>
            <?php foreach($jenisperalatans as $jp): ?>
                <option value="<?= $jp['id_jenisperalatan'] ?>"><?= $jp['jenis_peralatan'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="siri_peralatan" class="form-label">Nombor Siri / ID Peralatan <span class="text-danger">*</span></label>
        <input type="text" name="siri_peralatan" id="siri_peralatan" class="form-control" maxlength="50" required>
    </div>
    <div class="col-md-6">
        <label for="lokasi_peralatan" class="form-label"> Lokasi (Bangunan, Aras, Unit) <span class="text-danger">*</span></label>
        <input type="text" name="lokasi_peralatan" id="lokasi_peralatan" class="form-control" maxlength="255" required>
    </div>
    <div class="col-md-6">
        <label for="jenama_model" class="form-label">Jenama / Model <span class="text-danger">*</span></label>
        <input type="text" name="jenama_model" id="jenama_model" class="form-control" maxlength="100" required>
    </div>
    <div class="col-md-6">
        <label for="tarikh_dibeli" class="form-label"> Tarikh Dibeli / Diterima <span class="text-danger">*</span></label>
        <input type="date" name="tarikh_dibeli" id="tarikh_dibeli" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label for="tempoh_jaminan_peralatan" class="form-label">Tempoh Warranty Peralatan</label>
        <input type="text" name="tempoh_jaminan_peralatan" id="tempoh_jaminan_peralatan" class="form-control" maxlength="50">
    </div>
    <div class="col-md-6">
        <label for="expired_jaminan" class="form-label">Tarikh Luput Warranty Peralatan</label>
        <input type="date" name="expired_jaminan" id="expired_jaminan" class="form-control">
    </div>
    <div class="col-md-6">
        <label for="id_penyelenggaraan" class="form-label">Kaedah Penyelenggaraan <span class="text-danger">*</span></label>
        <select name="id_penyelenggaraan" id="id_penyelenggaraan" class="form-select" required>
            <option value="">-- Pilih Penyelenggaraan --</option>
            <?php foreach($penyelenggaraans as $p): ?>
                <option value="<?= $p['id_penyelenggaraan'] ?>"><?= $p['penyelenggaraan'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="id_pembekal" class="form-label"> Nama Pembekal / Kontraktor <span class="text-danger">*</span></label>
        <select name="id_pembekal" id="id_pembekal" class="form-select" required>
            <option value="">-- Pilih Pembekal / Kontraktor --</option>
            <?php foreach($pembekals as $pb): ?>
                <option value="<?= $pb['id_pembekal'] ?>"><?= $pb['nama_syarikat'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="kos_penyelenggaraan_tahunan" class="form-label">Kos Penyelenggaraan Tahunan (RM)</label>
        <input type="number" step="0.01" name="kos_penyelenggaraan_tahunan" id="kos_penyelenggaraan_tahunan" class="form-control" value="0.00">
    </div>
    <div class="col-md-6">
        <label for="tarikh_penyelenggaraan_terakhir" class="form-label">Tarikh Penyelenggaraan Terakhir</label>
        <input type="date" name="tarikh_penyelenggaraan_terakhir" id="tarikh_penyelenggaraan_terakhir" class="form-control">
    </div>
    <div class="col-md-6">
        <label for="pegawai_rujukan_peralatan" class="form-label">Pegawai Rujukan Peralatan <span class="text-danger">*</span></label>
        <select name="pegawai_rujukan_peralatan" id="pegawai_rujukan_peralatan" class="form-select" required>
            <option value="">-- Pilih Pegawai --</option>
            <?php foreach($userprofiles as $u): ?>
                <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?> (<?= $u['jawatan_user'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
