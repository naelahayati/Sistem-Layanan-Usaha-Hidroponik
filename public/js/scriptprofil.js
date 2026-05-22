// History Slider
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.history-slide');
    const prevBtn = document.querySelector('.prev-history');
    const nextBtn = document.querySelector('.next-history');
    const dotsContainer = document.querySelector('.dots-container');
    let currentSlide = 0;
    let slideInterval;

    // Buat dots
    if (dotsContainer && slides.length > 0) {
        slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (index === currentSlide) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });
    }

    const dots = document.querySelectorAll('.dot');

    function goToSlide(index) {
        slides[currentSlide].classList.remove('active');
        if (dots[currentSlide]) dots[currentSlide].classList.remove('active');
        currentSlide = (index + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
    }

    function nextSlide() { goToSlide(currentSlide + 1); }
    function prevSlide() { goToSlide(currentSlide - 1); }

    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);

    function startAutoSlide() {
        if (slideInterval) clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 5000);
    }
    
    function stopAutoSlide() {
        if (slideInterval) clearInterval(slideInterval);
    }

    startAutoSlide();
    
    const sliderContainer = document.querySelector('.history-slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }

    // Carousel Horizontal
    const carousel = document.querySelector('.carousel-horizontal');
    const prevCarousel = document.querySelector('.carousel-control.prev');
    const nextCarousel = document.querySelector('.carousel-control.next');

    if (prevCarousel && carousel) {
        prevCarousel.addEventListener('click', () => {
            carousel.scrollBy({ left: -350, behavior: 'smooth' });
        });
    }

    if (nextCarousel && carousel) {
        nextCarousel.addEventListener('click', () => {
            carousel.scrollBy({ left: 350, behavior: 'smooth' });
        });
    }
});