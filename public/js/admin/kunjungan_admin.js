// JavaScript untuk Kelola Kunjungan

document.addEventListener('DOMContentLoaded', function () {
    const kunjunganContainer = document.getElementById('kunjunganContainer');

    if (kunjunganContainer) {
        kunjunganContainer.addEventListener('click', function (e) {
            const actionTarget = e.target.closest('button, a.btn');
            if (actionTarget) {
                e.stopPropagation(); // Stop propagation so the card modal doesn't show
                
                if (actionTarget.classList.contains('deleteKunjunganBtn')) {
                    e.preventDefault();
                    const kunjunganId = actionTarget.getAttribute('data-id');
                    deleteKunjungan(kunjunganId);
                } else if (actionTarget.classList.contains('toggleStatusBtn')) {
                    e.preventDefault();
                    const kunjunganId = actionTarget.getAttribute('data-id');
                    toggleKunjunganStatus(kunjunganId);
                }
                // For edit link (<a>), navigation will happen naturally
                return;
            }

            const cardTarget = e.target.closest('.viewKunjunganCard');
            if (cardTarget) {
                const kunjunganData = JSON.parse(cardTarget.getAttribute('data-kunjungan'));
                showKunjunganModal(kunjunganData);
            }
        });
    }

    // Form Tambah Kunjungan
    const addKunjunganForm = document.getElementById('addKunjunganForm');
    if (addKunjunganForm) {
        addKunjunganForm.addEventListener('submit', function (e) {
            e.preventDefault();
            addKunjungan();
        });
    }

    // Form Edit Kunjungan
    const editKunjunganForm = document.getElementById('editKunjunganForm');
    if (editKunjunganForm) {
        editKunjunganForm.addEventListener('submit', function (e) {
            e.preventDefault();
            updateKunjungan();
        });
    }
});

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

function addKunjungan() {
    const form = document.getElementById('addKunjunganForm');
    const formData = new FormData(form);
    const csrfToken = getCsrfToken();

    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang menyimpan paket kunjungan baru.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    fetch('/admin/kunjungan/add', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            Swal.fire('Berhasil!', result.message, 'success').then(() => { if (result.redirect) { window.location.href = result.redirect; } else { location.reload(); } });
        } else {
            Swal.fire('Gagal!', result.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(err => Swal.fire('Error!', err.message, 'error'));
}

function loadKunjunganData(kunjunganId) {
    fetch(`/admin/kunjungan/get/${kunjunganId}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById('editKunjunganId').value = data.id;
        document.getElementById('editName').value = data.name;
        document.getElementById('editPrice').value = data.price;
        document.getElementById('editMinPeople').value = data.min_people;
        document.getElementById('editMaxPeople').value = data.max_people;
        document.getElementById('editDescription').value = data.description;

        const preview = document.getElementById('editImagePreview');
        if (data.image_url) {
            preview.src = data.image_url;
            preview.classList.remove('d-none');
        } else {
            preview.classList.add('d-none');
        }

        $('#editKunjunganModal').modal('show');
    })
    .catch(err => Swal.fire('Error!', 'Gagal memuat data kunjungan', 'error'));
}

function updateKunjungan() {
    const kunjunganId = document.getElementById('editKunjunganId').value;
    const form = document.getElementById('editKunjunganForm');
    const formData = new FormData(form);
    const csrfToken = getCsrfToken();

    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang memperbarui data kunjungan.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    fetch(`/admin/kunjungan/edit/${kunjunganId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            Swal.fire('Berhasil!', result.message, 'success').then(() => { if (result.redirect) { window.location.href = result.redirect; } else { location.reload(); } });
        } else {
            Swal.fire('Gagal!', result.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(err => Swal.fire('Error!', err.message, 'error'));
}

function deleteKunjungan(kunjunganId) {
    Swal.fire({
        title: 'Hapus Kunjungan?',
        text: 'Data ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/kunjungan/delete/${kunjunganId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    Swal.fire('Terhapus!', result.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error!', err.message, 'error'));
        }
    });
}

function showKunjunganModal(kunjungan) {
    document.getElementById('viewName').textContent = kunjungan.name;
    document.getElementById('viewPrice').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(kunjungan.price);
    document.getElementById('viewMinPeople').textContent = kunjungan.min_people + " Orang";
    document.getElementById('viewMaxPeople').textContent = kunjungan.max_people + " Orang";
    document.getElementById('viewDescription').textContent = kunjungan.description;

    const imgEl = document.getElementById('viewImage');
    const noImgEl = document.getElementById('viewNoImage');

    if (kunjungan.image_url) {
        imgEl.src = kunjungan.image_url;
        imgEl.classList.remove('d-none');
        noImgEl.classList.add('d-none');
        noImgEl.classList.remove('d-flex');
    } else {
        imgEl.classList.add('d-none');
        noImgEl.classList.remove('d-none');
        noImgEl.classList.add('d-flex');
    }

    $('#viewKunjunganModal').modal('show');
}

function toggleKunjunganStatus(kunjunganId) {
    Swal.fire({
        title: 'Ubah Status Paket?',
        text: 'Status aktif/non-aktif paket kunjungan akan diubah.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2d5a27',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/kunjungan/toggle/${kunjunganId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Status paket kunjungan berhasil diperbarui.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error!', err.message, 'error'));
        }
    });
}


