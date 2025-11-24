document.addEventListener('DOMContentLoaded', function() {

    const kaedahSelect = document.getElementById('kaedahPembangunan');
    const outsourceBox = document.getElementById('outsourceBox');
    const inhouseBox = document.getElementById('inhouseBox');

    const outsourceSelect = document.getElementById('outsourceSelect');
    const manualOutsource = document.getElementById('manualOutsource');

    const picBox = document.getElementById('picBox');
    const picSelect = document.getElementById('picSelect');
    const manualPIC = document.getElementById('manualPIC');

    // Show/hide based on kaedah
    kaedahSelect.addEventListener('change', function() {
        if (this.value === '2') { // outsource
            outsourceBox.style.display = 'block';
            inhouseBox.style.display = 'none';
            picBox.style.display = 'block';

        } else if (this.value === '1') { // inhouse
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

    // Select outsource company
    outsourceSelect.addEventListener('change', function () {
        const id = this.value;

        if (id === 'other') {
            manualOutsource.style.display = 'block';
            manualPIC.style.display = 'block';
            picSelect.innerHTML = '<option value="other">Tambah Baru...</option>';
            return;
        }

        manualOutsource.style.display = 'none';

        // Load PIC from DB
        fetch('ajax_get_pic.php?outsource_id=' + id)
        .then(response => response.json())
        .then(data => {
            picSelect.innerHTML = '<option value="">-- Pilih PIC --</option>';

            if (data && data.id_PIC) {
                picSelect.innerHTML += `
                    <option value="${data.id_PIC}">${data.nama_PIC}</option>
                `;
            }

            picSelect.innerHTML += '<option value="other">Tambah Baru...</option>';
        });
    });


    // Select PIC
    picSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            manualPIC.style.display = 'block';
        } else {
            manualPIC.style.display = 'none';
        }
    });
});
