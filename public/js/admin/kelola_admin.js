// JavaScript untuk Kelola Admin

document.addEventListener("DOMContentLoaded", function () {
    const adminTable = document.getElementById("adminTable");

    // Event Delegation untuk tombol di dalam tabel
    if (adminTable) {
        adminTable.addEventListener("click", function (e) {
            const target = e.target.closest("button");
            if (!target) return;

            const adminId = target.getAttribute("data-id");

            if (target.classList.contains("editAdminBtn")) {
                loadAdminData(adminId);
            } else if (target.classList.contains("deleteAdminBtn")) {
                deleteAdmin(adminId);
            } else if (target.classList.contains("toggle-password-btn")) {
                const wrapper = target.closest(".password-wrapper");
                const passwordText = wrapper.querySelector(".password-text");
                const passwordMasked =
                    wrapper.querySelector(".password-masked");
                const icon = target.querySelector("i");

                if (passwordText.style.display === "none") {
                    passwordText.style.display = "inline-block";
                    passwordMasked.style.display = "none";
                    icon.classList.replace("fa-eye", "fa-eye-slash");
                } else {
                    passwordText.style.display = "none";
                    passwordMasked.style.display = "inline-block";
                    icon.classList.replace("fa-eye-slash", "fa-eye");
                }
            }
        });
    }

    // Toggle Password di Modal Tambah
    const toggleAddPassword = document.querySelector(".toggle-add-password");
    if (toggleAddPassword) {
        toggleAddPassword.addEventListener("click", function () {
            const passwordInput = document.getElementById("addPassword");
            const icon = this.querySelector("i");
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            icon.classList.toggle("fa-eye", !isPassword);
            icon.classList.toggle("fa-eye-slash", isPassword);
        });
    }

    // Toggle Password di Modal Edit
    const toggleEditPassword = document.querySelector(".toggle-edit-password");
    if (toggleEditPassword) {
        toggleEditPassword.addEventListener("click", function () {
            const passwordInput = document.getElementById("editPassword");
            const icon = this.querySelector("i");
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            icon.classList.toggle("fa-eye", !isPassword);
            icon.classList.toggle("fa-eye-slash", isPassword);
        });
    }

    // Form Tambah Admin
    const addAdminForm = document.getElementById("addAdminForm");
    if (addAdminForm) {
        addAdminForm.addEventListener("submit", function (e) {
            e.preventDefault();
            addAdmin();
        });
    }

    // Form Edit Admin
    const editAdminForm = document.getElementById("editAdminForm");
    if (editAdminForm) {
        editAdminForm.addEventListener("submit", function (e) {
            e.preventDefault();
            updateAdmin();
        });
    }
});

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

function addAdmin() {
    const form = document.getElementById("addAdminForm");
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const csrfToken = getCsrfToken();

    fetch("/admin/admin/add", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(data),
    })
        .then((res) => res.json())
        .then((result) => {
            if (result.success) {
                Swal.fire("Berhasil!", result.message, "success").then(() =>
                    location.reload(),
                );
            } else {
                Swal.fire(
                    "Gagal!",
                    result.message || "Terjadi kesalahan",
                    "error",
                );
            }
        })
        .catch((err) => Swal.fire("Error!", err.message, "error"));
}

function loadAdminData(adminId) {
    fetch(`/admin/admin/get/${adminId}`)
        .then((res) => res.json())
        .then((data) => {
            document.getElementById("editAdminId").value = data.id;
            document.getElementById("editUsername").value = data.username;
            document.getElementById("editPassword").value = "";
            $("#editAdminModal").modal("show");
        })
        .catch((err) => Swal.fire("Error!", "Gagal memuat data", "error"));
}

function updateAdmin() {
    const adminId = document.getElementById("editAdminId").value;
    const form = document.getElementById("editAdminForm");
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const csrfToken = getCsrfToken();

    fetch(`/admin/admin/edit/${adminId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(data),
    })
        .then((res) => res.json())
        .then((result) => {
            if (result.success) {
                Swal.fire("Berhasil!", result.message, "success").then(() =>
                    location.reload(),
                );
            } else {
                Swal.fire(
                    "Gagal!",
                    result.message || "Terjadi kesalahan",
                    "error",
                );
            }
        })
        .catch((err) => Swal.fire("Error!", err.message, "error"));
}

function deleteAdmin(adminId) {
    Swal.fire({
        title: "Hapus Admin?",
        text: "Data ini akan dihapus secara permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Ya, Hapus!",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/admin/delete/${adminId}`, {
                method: "DELETE",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                },
            })
                .then((res) => res.json())
                .then((result) => {
                    if (result.success) {
                        Swal.fire("Terhapus!", result.message, "success").then(
                            () => location.reload(),
                        );
                    } else {
                        Swal.fire("Gagal!", result.message, "error");
                    }
                })
                .catch((err) => Swal.fire("Error!", err.message, "error"));
        }
    });
}
