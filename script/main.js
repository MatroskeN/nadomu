const swiper = new Swiper('.serviceSlider', {
    // Optional parameters
    direction: 'horizontal',
    loop: true,
    slidesPerView: 3,
    spaceBetween: 0,

    // Navigation arrows
    navigation: {
        nextEl: '.button-next',
        prevEl: '.button-prev',
    },
});

const swiper1 = new Swiper('.specialistsSlider', {
    // Optional parameters
    direction: 'horizontal',
    loop: true,
    slidesPerView: 4,
    spaceBetween: 0,

    // Navigation arrows
    navigation: {
        nextEl: '.spec-next',
        prevEl: '.spec-prev',
    },
});