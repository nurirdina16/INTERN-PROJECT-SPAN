<div class="section-title"><i class="bi bi-person-badge"></i> MAKLUMAT PENGGUNA</div>
<div class="row g-3">
    <div class="col-md-6">
        <label>Nama Pengguna</label>
        <input type="text" name="nama_user" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Jawatan</label>
        <input type="text" name="jawatan_user" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Emel</label>
        <input type="email" name="emel_user" class="form-control">
    </div>
    <div class="col-md-6">
        <label>No Telefon</label>
        <input type="text" name="notelefon_user" class="form-control">
    </div>
    <div class="col-md-6">
        <label>No Faks</label>
        <input type="text" name="fax_user" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Bahagian / Unit</label>
        <select name="id_bahagianunit" class="form-select">
            <option value="">-- Pilih Bahagian/Unit --</option>
            <?php foreach($bahagianunits as $b): ?>
                <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
