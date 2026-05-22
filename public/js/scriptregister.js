document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Mengambil nilai dari input
    const formData = {
        nama: document.getElementById('nama').value,
        username: document.getElementById('username').value,
        alamat: document.getElementById('alamat').value,
        nohp: document.getElementById('nohp').value,
        umur: document.getElementById('umur').value
    };

    console.log("Data Registrasi:", formData);

    // Simulasi pendaftaran berhasil
    alert("Akun untuk " + formData.nama + " berhasil dibuat!");

    // Biasanya di sini kamu akan mengarahkan ke halaman login
    // window.location.href = "login.html";
});
