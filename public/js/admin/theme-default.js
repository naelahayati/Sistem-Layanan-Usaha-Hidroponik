document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const pushMenuBtn = document.querySelector('[data-widget="pushmenu"]');

    const sidebarStatus = localStorage.getItem('sidebar-status');
    if (sidebarStatus === 'collapsed') {
        body.classList.add('sidebar-collapse');
    } else {
        body.classList.remove('sidebar-collapse');
    }

    if (pushMenuBtn) {
        pushMenuBtn.addEventListener('click', function () {
            setTimeout(() => {
                if (body.classList.contains('sidebar-collapse')) {
                    localStorage.setItem('sidebar-status', 'collapsed');
                } else {
                    localStorage.setItem('sidebar-status', 'expanded');
                }
            }, 100);
        });
    }

    const mainSidebar = document.querySelector('.main-sidebar');
    const sidebarEl = document.querySelector('.sidebar');
    let lastScrollTop = 0;

    if (mainSidebar && sidebarEl) {
        mainSidebar.addEventListener('mouseleave', function () {
            lastScrollTop = sidebarEl.scrollTop;
        });

        mainSidebar.addEventListener('mouseenter', function () {
            sidebarEl.scrollTop = lastScrollTop;
        });

        if (pushMenuBtn) {
            pushMenuBtn.addEventListener('click', function () {
                setTimeout(() => {
                    sidebarEl.scrollTop = lastScrollTop;
                }, 350);
            });
        }
    }

    /* Tutup sidebar saat klik overlay di mobile */
    document.addEventListener('click', function (e) {
        if (window.innerWidth > 991.98) return;
        if (!body.classList.contains('sidebar-open')) return;
        if (e.target.closest('.main-sidebar')) return;
        if (e.target.closest('[data-widget="pushmenu"]')) return;
        if (pushMenuBtn) pushMenuBtn.click();
    });
});

window.addEventListener('load', function () {
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            const wrapper = document.querySelector('.wrapper');
            if (wrapper) {
                wrapper.style.transition = 'opacity 0.35s ease';
                wrapper.style.opacity = '1';
            }
        });
    });
});
