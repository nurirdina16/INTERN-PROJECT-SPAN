document.addEventListener('DOMContentLoaded', function() {
    const kaedahSelect = document.getElementById('kaedahPembangunan');
    const outsourceBox = document.getElementById('outsourceBox');
    const inhouseBox = document.getElementById('inhouseBox');
    const outsourceSelect = document.getElementById('outsourceSelect');
    const manualOutsource = document.getElementById('manualOutsource');
    
    const picBox = document.getElementById('picBox');
    const picSelect = document.getElementById('picSelect');
    const manualPIC = document.getElementById('manualPIC');

    kaedahSelect.addEventListener('change', function() {
        const val = this.value;
        if(val === '2') { // outsource ID
            outsourceBox.style.display = 'block';
            inhouseBox.style.display = 'none';
            picBox.style.display = 'block';
        } else if(val === '1') { // inhouse ID
            inhouseBox.style.display = 'block';
            outsourceBox.style.display = 'none';
            manualOutsource.style.display = 'none';
            picBox.style.display = 'none';
        } else {
            outsourceBox.style.display = 'none';
            inhouseBox.style.display = 'none';
            picBox.style.display = 'none';
        }
    });

    // Bila pilih syarikat
    outsourceSelect.addEventListener('change', function() {
        if(this.value === 'other') {
            manualOutsource.style.display = 'flex';
            picSelect.innerHTML = '<option value="other">Other</option>';
            manualPIC.style.display = 'flex';
        } else {
            manualOutsource.style.display = 'none';
            manualPIC.style.display = 'none';
            // fetch PIC untuk syarikat terpilih
            fetch('../app/ajax_get_pic.php?outsource_id=' + this.value)
                .then(res => res.json())
                .then(data => {
                    picSelect.innerHTML = '<option value="">-- Pilih PIC --</option><option value="other">Other</option>';
                    data.forEach(pic => {
                        picSelect.innerHTML += `<option value="${pic.id_PIC}">${pic.nama_PIC}</option>`;
                    });
                });
        }
    });

    // Bila pilih PIC
    picSelect.addEventListener('change', function() {
        if(this.value === 'other') {
            manualPIC.style.display = 'flex';
        } else {
            manualPIC.style.display = 'none';
        }
    });
});
