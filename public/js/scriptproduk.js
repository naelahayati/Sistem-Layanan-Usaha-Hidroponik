(function () {
    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    ready(function () {
        var boxes = document.querySelectorAll('.produk-photo-box img');
        Array.prototype.forEach.call(boxes, function (img) {
            var box = img.parentElement;
            if (!box) return;

            var span = box.querySelector('span');

            function showPlaceholder() {
                if (!span) return;
                span.style.display = 'flex';
                span.style.justifyContent = 'center';
                span.style.alignItems = 'center';
            }

            function hidePlaceholder() {
                if (!span) return;
                span.style.display = 'none';
            }

            // Kalau gambar sudah ke-load sebelum script jalan
            if (img.complete && img.naturalWidth > 0) {
                hidePlaceholder();
            }

            img.addEventListener('load', function () {
                hidePlaceholder();
            });

            img.addEventListener('error', function () {
                img.style.display = 'none';
                showPlaceholder();
            });
        });

        // Parallax ringan untuk gambar produk saat scroll.
        // Tujuan: saat user scroll ke atas, gambar ikut "naik" (translateY negatif).
        var photoBoxes = document.querySelectorAll('.produk-photo-box');
        var ticking = false;

        function clamp(n, min, max) {
            return Math.max(min, Math.min(max, n));
        }

        function updateParallax() {
            var viewportCenter = window.innerHeight * 0.5;

            Array.prototype.forEach.call(photoBoxes, function (box) {
                var rect = box.getBoundingClientRect();

                // Jika box jauh di luar layar, biar lebih ringan kinerjanya.
                if (rect.bottom < 0 || rect.top > window.innerHeight) {
                    box.style.transform = 'translateY(0px)';
                    return;
                }

                var distanceFromCenter = rect.top - viewportCenter; // positif kalau di bawah center

                // Semakin jauh dari center, semakin besar perpindahan (dibatasi).
                var translateY = clamp((-distanceFromCenter / viewportCenter) * 22, -26, 26);
                box.style.transform = 'translateY(' + translateY + 'px)';
            });

            ticking = false;
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(updateParallax);
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        updateParallax();
    });
})();

