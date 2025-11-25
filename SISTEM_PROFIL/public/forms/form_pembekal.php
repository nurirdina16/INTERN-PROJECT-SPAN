<div class="section-title"><i class="bi bi-people"></i> MAKLUMAT PEMBEKAL</div>
<div class="row g-3">
    <div class="col-md-6">
        <label>Nama Syarikat</label>
        <input type="text" name="nama_syarikat" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Tempoh Kontrak</label>
        <input type="text" name="tempoh_kontrak" class="form-control">
    </div>
    <div class="col-12">
        <label>Alamat Syarikat</label>
        <textarea name="alamat_syarikat" class="form-control" rows="3"></textarea>
    </div>
    <div class="col-md-6">
        <label>PIC</label>
        <select name="pic_id" class="form-select" id="picSelect">
            <option value="">-- Pilih PIC --</option>
            <?php foreach($pics as $p): ?>
                <option value="<?= $p['id_PIC'] ?>"><?= $p['nama_PIC'] ?></option>
            <?php endforeach; ?>
            <option value="other">Tambah Baru</option>
        </select>
    </div>
    <div id="manualPic" style="display:none;" class="row g-2 mt-2">
        <div class="col-md-6"><input type="text" name="manual_pic_nama" class="form-control" placeholder="Nama PIC"></div>
        <div class="col-md-6"><input type="email" name="manual_pic_emel" class="form-control" placeholder="Emel PIC"></div>
        <div class="col-md-6"><input type="text" name="manual_pic_telefon" class="form-control" placeholder="No Telefon PIC"></div>
        <div class="col-md-6"><input type="text" name="manual_pic_fax" class="form-control" placeholder="Fax PIC"></div>
        <div class="col-md-6"><input type="text" name="manual_pic_jawatan" class="form-control" placeholder="Jawatan PIC"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const picSelect = document.getElementById('picSelect');
    const manualPicDiv = document.getElementById('manualPic');
    picSelect.addEventListener('change', function(){
        manualPicDiv.style.display = this.value === 'other' ? 'flex' : 'none';
    });
});
</script>
