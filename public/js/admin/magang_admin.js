// JavaScript untuk Kelola Magang

document.addEventListener('DOMContentLoaded', function () {
    const magangContainer = document.getElementById('magangContainer');

    if (magangContainer) {
        magangContainer.addEventListener('click', function (e) {
            const actionTarget = e.target.closest('button, a.btn');
            if (actionTarget) {
                e.stopPropagation(); // Stop propagation so the card modal doesn't show
                
                if (actionTarget.classList.contains('deleteMagangBtn')) {
                    e.preventDefault();
                    const magangId = actionTarget.getAttribute('data-id');
                    deleteMagang(magangId);
                } else if (actionTarget.classList.contains('toggleStatusBtn')) {
                    e.preventDefault();
                    const magangId = actionTarget.getAttribute('data-id');
                    toggleMagangStatus(magangId);
                }
                // For edit link (<a>), navigation will happen naturally
                return;
            }

            const cardTarget = e.target.closest('.viewMagangCard');
            if (cardTarget) {
                const magangData = JSON.parse(cardTarget.getAttribute('data-magang'));
                showMagangModal(magangData);
            }
        });
    }

    // Form Tambah Magang
    const addMagangForm = document.getElementById('addMagangForm');
    if (addMagangForm) {
        addMagangForm.addEventListener('submit', function (e) {
            e.preventDefault();
            addMagang();
        });
    }

    // Form Edit Magang
    const editMagangForm = document.getElementById('editMagangForm');
    if (editMagangForm) {
        editMagangForm.addEventListener('submit', function (e) {
            e.preventDefault();
            updateMagang();
        });
    }
});

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

function addMagang() {
    const form = document.getElementById('addMagangForm');
    const formData = new FormData(form);
    const csrfToken = getCsrfToken();

    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang menyimpan paket magang baru.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    fetch('/admin/magang/add', {
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

function loadMagangData(magangId) {
    fetch(`/admin/magang/get/${magangId}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById('editMagangId').value = data.id;
        document.getElementById('editName').value = data.name;
        document.getElementById('editPrice').value = data.price;
        document.getElementById('editDescription').value = data.description;

        const preview = document.getElementById('editImagePreview');
        if (data.image_url) {
            preview.src = data.image_url;
            preview.classList.remove('d-none');
        } else {
            preview.classList.add('d-none');
        }

        $('#editMagangModal').modal('show');
    })
    .catch(err => Swal.fire('Error!', 'Gagal memuat data magang', 'error'));
}

function updateMagang() {
    const magangId = document.getElementById('editMagangId').value;
    const form = document.getElementById('editMagangForm');
    const formData = new FormData(form);
    const csrfToken = getCsrfToken();

    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang memperbarui data magang.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    fetch(`/admin/magang/edit/${magangId}`, {
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

function deleteMagang(magangId) {
    Swal.fire({
        title: 'Hapus Magang?',
        text: 'Data ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/magang/delete/${magangId}`, {
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

function showMagangModal(magang) {
    document.getElementById('viewName').textContent = magang.name;
    document.getElementById('viewPrice').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(magang.price);
    document.getElementById('viewDescription').textContent = magang.description;

    const imgEl = document.getElementById('viewImage');
    const noImgEl = document.getElementById('viewNoImage');

    if (magang.image_url) {
        imgEl.src = magang.image_url;
        imgEl.classList.remove('d-none');
        noImgEl.classList.add('d-none');
        noImgEl.classList.remove('d-flex');
    } else {
        imgEl.classList.add('d-none');
        noImgEl.classList.remove('d-none');
        noImgEl.classList.add('d-flex');
    }

    $('#viewMagangModal').modal('show');
}

function toggleMagangStatus(magangId) {
    Swal.fire({
        title: 'Ubah Status Paket?',
        text: 'Status aktif/non-aktif paket magang akan diubah.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2d5a27',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/magang/toggle/${magangId}`, {
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
                        text: 'Status paket magang berhasil diperbarui.',
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


