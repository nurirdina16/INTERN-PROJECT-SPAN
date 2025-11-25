<div class="section-title"><i class="bi bi-window-stack"></i> MAKLUMAT SISTEM</div>
<div class="row g-3">
    <div class="col-md-6">
        <label>Nama Sistem</label>
        <input type="text" name="nama_sistem" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Pemilik Sistem</label>
        <select name="id_pemilik_sistem" class="form-select">
            <option value="">-- Pilih Pemilik --</option>
            <?php foreach($bahagianunits as $b): ?>
                <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12">
        <label>Objektif Sistem</label>
        <textarea name="objektif_sistem" class="form-control" rows="3"></textarea>
    </div>
    <div class="col-md-4">
        <label>Tarikh Mula Pembangunan Sistem</label>
        <input type="date" name="tarikh_mula" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Tarikh Siap Pembangunan Sistem</label>
        <input type="date" name="tarikh_siap" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Tarikh Guna Pembangunan Sistem</label>
        <input type="date" name="tarikh_guna" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Bilangan Pengguna</label>
        <input type="text" name="bil_pengguna" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Bilangan Modul Sistem</label>
        <input type="text" name="bil_modul" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Kategori Sistem</label>
        <select name="id_kategori" class="form-select">
            <option value="">-- Pilih Kategori --</option>
            <?php foreach($kategoris as $k): ?>
                <option value="<?= $k['id_kategori'] ?>"><?= $k['kategori'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label>Bahasa Pengaturcaraan</label>
        <input type="text" name="bahasa_pengaturcaraan" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Jenis Pangkalan Data</label>
        <input type="text" name="pangkalan_data" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Rangkaian</label>
        <input type="text" name="rangkaian" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Integrasi Sistem Lain</label>
        <input type="text" name="integrasi" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Penyelenggaraan Sistem</label>
        <select name="id_penyelenggaraan" class="form-select">
            <option value="">-- Pilih Penyelenggaraan --</option>
            <?php foreach($penyelenggaraans as $p): ?>
                <option value="<?= $p['id_penyelenggaraan'] ?>"><?= $p['penyelenggaraan'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <!-- CONDITIONAL -->
    <div class="col-md-6">
        <label>Kaedah Pembangunan</label>
        <select name="id_kaedahpembangunan" id="kaedahPembangunan" class="form-select">
            <option value="">-- Pilih Kaedah --</option>
            <?php foreach($kaedahPembangunans as $kp): ?>
                <option value="<?= $kp['id_kaedahPembangunan'] ?>"><?= $kp['kaedahPembangunan'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6" id="divPembekal" style="display:none;">
        <label>Pembekal</label>
        <select name="id_pembekal" class="form-select">
            <option value="">-- Pilih Pembekal --</option>
            <?php foreach($pembekals as $pb): ?>
                <option value="<?= $pb['id_pembekal'] ?>"><?= $pb['nama_syarikat'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6" id="divInhouse" style="display:none;">
        <label>Dalaman</label>
        <select name="inhouse" class="form-select">
            <option value="">-- Pilih Bahagian Bertanggungjawab --</option>
            <?php foreach($bahagianunits as $b): ?>
                <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kaedahSelect = document.getElementById('kaedahPembangunan');
            const divPembekal = document.getElementById('divPembekal');
            const divInhouse = document.getElementById('divInhouse');

            kaedahSelect.addEventListener('change', function() {
                // Replace these values with your actual IDs for "Pembekal" and "Dalaman"
                const pembekalID = '2'; // Example: ID for Pembekal
                const dalamanID = '1';  // Example: ID for Dalaman/In-house

                if (this.value === pembekalID) {
                    divPembekal.style.display = 'block';
                    divInhouse.style.display = 'none';
                } else if (this.value === dalamanID) {
                    divPembekal.style.display = 'none';
                    divInhouse.style.display = 'block';
                } else {
                    divPembekal.style.display = 'none';
                    divInhouse.style.display = 'none';
                }
            });
        });
    </script>

    <div class="col-md-4">
        <label>Tarikh Dibeli / Diterima</label>
        <input type="date" name="tarikh_dibeli" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Tempoh Warranty Sistem</label>
        <input type="text" name="tempoh_jaminan_sistem" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Tarikh Luput Warranty Sistem</label>
        <input type="date" name="expired_jaminan_sistem" class="form-control">
    </div>
    <div class="col-md-3">
        <label> Kos Keseluruhan Pembangunan (RM)</label>
        <input type="number" step="0.01" name="kos_keseluruhan" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Kos Perkakasan (RM)</label>
        <input type="number" step="0.01" name="kos_perkakasan" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Kos Perisian (RM)</label>
        <input type="number" step="0.01" name="kos_perisian" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Kos Lesen Perisian (RM)</label>
        <input type="number" step="0.01" name="kos_lesen_perisian" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Kos Penyelenggaraan (RM)</label>
        <input type="number" step="0.01" name="kos_penyelenggaraan" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Kos Lain (RM)</label>
        <input type="number" step="0.01" name="kos_lain" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Kategori Jenis Pengguna</label>
        <select name="id_kategoriuser" class="form-select">
            <option value="">-- Pilih Kategori Pengguna --</option>
            <?php foreach($kategoris as $k): ?>
                <option value="<?= $k['id_kategori'] ?>"><?= $k['kategori'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label>Pengurus Akses Sistem</label>
        <select name="pengurus_akses" class="form-select">
            <option value="">-- Pilih Bahagian / Unit --</option>
            <?php foreach($bahagianunits as $b): ?>
                <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label>Pegawai Rujukan Sistem</label>
        <select name="pegawai_rujukan_sistem" class="form-select">
            <option value="">-- Pilih Pegawai --</option>
            <?php foreach($userprofiles as $u): ?>
                <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
