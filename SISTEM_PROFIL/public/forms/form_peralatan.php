<div class="section-title"><i class="bi bi-hdd"></i> MAKLUMAT PERALATAN</div>
<div class="row g-3">
    <div class="col-md-6">
        <label>Nama Peralatan</label>
        <input type="text" name="nama_peralatan" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Jenis Peralatan</label>
        <select name="id_jenisperalatan" class="form-select">
            <option value="">-- Pilih Jenis --</option>
            <?php foreach($jenisperalatans as $jp): ?>
                <option value="<?= $jp['id_jenisperalatan'] ?>"><?= $jp['jenisperalatan'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label>Nombor Siri / ID Peralatan</label>
        <input type="text" name="siri_peralatan" class="form-control">
    </div>
    <div class="col-md-6">
        <label> Lokasi (Bangunan, Aras, Unit)</label>
        <input type="text" name="lokasi_peralatan" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Jenama / Model</label>
        <input type="text" name="jenama_model" class="form-control">
    </div>
    <div class="col-md-6">
        <label> Tarikh Dibeli / Diterima</label>
        <input type="date" name="tarikh_dibeli" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Tempoh Warranty Peralatan</label>
        <input type="text" name="tempoh_jaminan_peralatan" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Tarikh Luput Warranty Peralatan</label>
        <input type="date" name="expired_jaminan" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Kaedah Penyelenggaraan</label>
        <select name="id_penyelenggaraan" class="form-select">
            <option value="">-- Pilih Penyelenggaraan --</option>
            <?php foreach($penyelenggaraans as $p): ?>
                <option value="<?= $p['id_penyelenggaraan'] ?>"><?= $p['penyelenggaraan'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label> Nama Pembekal / Kontraktor</label>
        <select name="id_pembekal" class="form-select">
            <option value="">-- Pilih Pembekal / Kontraktor --</option>
            <?php foreach($pembekals as $pb): ?>
                <option value="<?= $pb['id_pembekal'] ?>"><?= $pb['nama_syarikat'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label>Kos Penyelenggaraan Tahunan (RM)</label>
        <input type="number" step="0.01" name="kos_penyelenggaraan_tahunan" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Tarikh Penyelenggaraan Terakhir</label>
        <input type="date" name="tarikh_penyelenggaraan_terakhir" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Pegawai Rujukan Peralatan</label>
        <select name="pegawai_rujukan_peralatan" class="form-select">
            <option value="">-- Pilih Pegawai --</option>
            <?php foreach($userprofiles as $u): ?>
                <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
