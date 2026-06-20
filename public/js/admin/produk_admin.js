// JavaScript untuk Kelola Produk

document.addEventListener('DOMContentLoaded', function () {
    const productContainer = document.getElementById('productContainer');

    if (productContainer) {
        productContainer.addEventListener('click', function (e) {
            const actionTarget = e.target.closest('button, a.btn');
            if (actionTarget) {
                e.stopPropagation(); // Stop propagation so the card modal doesn't show
                
                if (actionTarget.classList.contains('deleteProductBtn')) {
                    e.preventDefault();
                    const productId = actionTarget.getAttribute('data-id');
                    deleteProduct(productId);
                } else if (actionTarget.classList.contains('toggleStatusBtn')) {
                    e.preventDefault();
                    const productId = actionTarget.getAttribute('data-id');
                    toggleProductStatus(productId);
                }
                // For edit link (<a>), navigation will happen naturally since we don't preventDefault()
                return;
            }

            const cardTarget = e.target.closest('.viewProductCard');
            if (cardTarget) {
                const productData = JSON.parse(cardTarget.getAttribute('data-product'));
                showProductModal(productData);
            }
        });
    }

    // Form Tambah Produk
    const addProductForm = document.getElementById('addProductForm');
    if (addProductForm) {
        addProductForm.addEventListener('submit', function (e) {
            e.preventDefault();
            addProduct();
        });
    }

    // Form Edit Produk
    const editProductForm = document.getElementById('editProductForm');
    if (editProductForm) {
        editProductForm.addEventListener('submit', function (e) {
            e.preventDefault();
            updateProduct();
        });
    }
});

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

function addProduct() {
    const form = document.getElementById('addProductForm');
    const formData = new FormData(form);
    const csrfToken = getCsrfToken();

    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang menyimpan produk baru.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    fetch('/admin/produk/add', {
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

function loadProductData(productId) {
    fetch(`/admin/produk/get/${productId}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById('editProductId').value = data.id;
        document.getElementById('editName').value = data.name;
        document.getElementById('editPrice').value = data.price;
        document.getElementById('editStock').value = data.stock;
        document.getElementById('editDescription').value = data.description || '';

        const preview = document.getElementById('editImagePreview');
        if (data.image_url) {
            preview.src = data.image_url;
            preview.classList.remove('d-none');
        } else {
            preview.classList.add('d-none');
        }

        $('#editProductModal').modal('show');
    })
    .catch(err => Swal.fire('Error!', 'Gagal memuat data produk', 'error'));
}

function updateProduct() {
    const productId = document.getElementById('editProductId').value;
    const form = document.getElementById('editProductForm');
    const formData = new FormData(form);
    const csrfToken = getCsrfToken();

    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang memperbarui data produk.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    fetch(`/admin/produk/edit/${productId}`, {
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

function deleteProduct(productId) {
    Swal.fire({
        title: 'Hapus Produk?',
        text: 'Data ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/produk/delete/${productId}`, {
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

function showProductModal(product) {
    document.getElementById('viewName').textContent = product.name;
    document.getElementById('viewPrice').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(product.price);
    document.getElementById('viewStock').textContent = product.stock;
    document.getElementById('viewCartStock').textContent = product.cart_stock || 0;
    document.getElementById('viewDescription').textContent = product.description || 'Tidak ada deskripsi';

    const imgEl = document.getElementById('viewImage');
    const noImgEl = document.getElementById('viewNoImage');

    if (product.image_url) {
        imgEl.src = product.image_url;
        imgEl.classList.remove('d-none');
        noImgEl.classList.add('d-none');
        noImgEl.classList.remove('d-flex');
    } else {
        imgEl.classList.add('d-none');
        noImgEl.classList.remove('d-none');
        noImgEl.classList.add('d-flex');
    }

    $('#viewProductModal').modal('show');
}

function toggleProductStatus(productId) {
    Swal.fire({
        title: 'Ubah Status Produk?',
        text: 'Status aktif/non-aktif produk akan diubah.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2d5a27',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/produk/toggle/${productId}`, {
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
                        text: 'Status produk berhasil diperbarui.',
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


