document.querySelector('form').addEventListener('submit', function() {
    const loginBtn = document.querySelector('.login-btn');
    loginBtn.innerHTML = "Memproses...";
    loginBtn.style.opacity = "0.6";
    loginBtn.style.cursor = "not-allowed";
});
