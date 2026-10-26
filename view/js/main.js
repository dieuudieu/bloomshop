/**
 * ==============================================================================
 * BLOOMSHOP - MAIN CLIENT JAVASCRIPT
 * Xử lý tương tác giao diện, chuyển slide mượt mà & tự động chuyển sau 3-5 giây
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    // --------------------------------------------------------------------------
    // 1. HERO BANNER STUDIO CAROUSEL (#bloomHeroCarousel)
    // --------------------------------------------------------------------------
    const heroCarouselEl = document.getElementById('bloomHeroCarousel');
    if (heroCarouselEl) {
        // Khởi tạo Bootstrap Carousel:
        // - interval: 4000 (4 giây, nằm trong khoảng 3 - 5s theo yêu cầu)
        // - pause: false (không bị đứng yên vĩnh viễn khi rê chuột)
        // - ride: 'carousel' (tự động chạy ngay khi tải trang)
        const heroCarousel = bootstrap.Carousel.getOrCreateInstance(heroCarouselEl, {
            interval: 4000,
            pause: false,
            ride: 'carousel',
            wrap: true,
            touch: true
        });

        // Đảm bảo bắt đầu tự động chuyển slide
        heroCarousel.cycle();

        const prevBtn = heroCarouselEl.querySelector('.hero-arrow-btn[data-bs-slide="prev"]') || document.getElementById('heroPrevBtn');
        const nextBtn = heroCarouselEl.querySelector('.hero-arrow-btn[data-bs-slide="next"]') || document.getElementById('heroNextBtn');
        const dashIndicators = heroCarouselEl.querySelectorAll('.hero-dash-indicators button');

        // Hàm cập nhật trạng thái thanh gạch (dash indicators) theo slide hiện tại
        function updateActiveDash(targetIndex) {
            dashIndicators.forEach((btn, idx) => {
                if (idx === targetIndex) {
                    btn.classList.add('active');
                    btn.setAttribute('aria-current', 'true');
                } else {
                    btn.classList.remove('active');
                    btn.removeAttribute('aria-current');
                }
            });
        }

        // Lắng nghe sự kiện chuyển slide để đồng bộ thanh gạch chỉ số
        heroCarouselEl.addEventListener('slide.bs.carousel', function (e) {
            const toIndex = typeof e.to !== 'undefined' ? e.to : Array.from(heroCarouselEl.querySelectorAll('.carousel-item')).indexOf(e.relatedTarget);
            if (toIndex >= 0) {
                updateActiveDash(toIndex);
            }
        });

        // Xử lý nút Chuyển lùi (Prev)
        if (prevBtn) {
            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                heroCarousel.prev();
                // Khởi động lại đếm giờ 4s để người dùng có trọn vẹn 4s ngắm slide mới
                heroCarousel.cycle();
            });
        }

        // Xử lý nút Chuyển tiến (Next)
        if (nextBtn) {
            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                heroCarousel.next();
                heroCarousel.cycle();
            });
        }

        // Xử lý khi người dùng bấm trực tiếp vào từng thanh gạch (dash indicators)
        dashIndicators.forEach((button, index) => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                heroCarousel.to(index);
                heroCarousel.cycle();
            });
        });
    }

    // --------------------------------------------------------------------------
    // 2. BANNER QUẢNG CÁO GIỮA KHỐI HOA (#middlePromoCarousel)
    // --------------------------------------------------------------------------
    const middlePromoEl = document.getElementById('middlePromoCarousel');
    if (middlePromoEl) {
        const middleCarousel = bootstrap.Carousel.getOrCreateInstance(middlePromoEl, {
            interval: 4000,
            pause: false,
            ride: 'carousel',
            wrap: true,
            touch: true
        });
        middleCarousel.cycle();
    }
});
