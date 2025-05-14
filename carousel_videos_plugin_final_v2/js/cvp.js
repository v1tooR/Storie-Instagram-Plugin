document.addEventListener("DOMContentLoaded", function () {
    function playActiveVideo(swiper) {
        swiper.slides.forEach(slide => {
            const video = slide.querySelector("video");
            if (video) {
                video.pause();
                video.currentTime = 0;
            }
        });
        const activeSlide = swiper.slides[swiper.activeIndex];
        const video = activeSlide ? activeSlide.querySelector("video") : null;
        if (video) {
            video.loop = true;
            video.muted = true;
            video.playsInline = true;
            video.play().catch(() => {});
        }
    }

    const swiper = new Swiper(".cvp-swiper", {
        loop: true,
        centeredSlides: true,
        slidesPerView: 1,
        spaceBetween: 20,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            768: { slidesPerView: 3 },
            1024: { slidesPerView: 5 },
        },
        on: {
            init: function () {
                playActiveVideo(this);
            },
            transitionEnd: function () {
                playActiveVideo(this);
            }
        }
    });
});
