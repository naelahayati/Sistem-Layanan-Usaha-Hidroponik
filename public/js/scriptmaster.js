document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.nav-links a');
    const btnAkun = document.getElementById('btnAkun');

    // Logika untuk mengubah link aktif saat diklik
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Event handler untuk tombol Akun
    btnAkun.addEventListener('click', () => {
        //alert('Tombol Akun diklik!');
    });
});
