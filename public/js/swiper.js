const swiper = new Swiper('.serviceSlider', {
    direction: 'horizontal',
    loop: true,
    slidesPerView: 1,

    navigation: {
        nextEl: '.button-next',
        prevEl: '.button-prev',
    },

    breakpoints: {
        370: {
            slidesPerView: 2,
        },
        526: {
            slidesPerView: 3,
        },
    }
});

const swiper1 = new Swiper('.specialistsSlider', {
    direction: 'horizontal',
    loop:true,

    // Navigation arrows
    navigation: {
        nextEl: '.spec-next',
        prevEl: '.spec-prev',
    },

    breakpoints: {
        320: {
            slidesPerView: 1,
        },
        650: {
            slidesPerView: 2,
            spaceBetween: 14,
            centeredSlides: false,
        },
        1050: {
            slidesPerView: 3,
        },
        1251: {
            slidesPerView: 4,
        }
    }
});

const swiper2 = new Swiper('.reviewSlider', {
    direction: 'horizontal',
    loop: true,
    slidesPerView: 2,
    spaceBetween: 25,

    // Navigation arrows
    navigation: {
        nextEl: '.review-next',
        prevEl: '.review-prev',
    },
    pagination: {
        el: '.review-pagination',
        clickable: 'true'
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
        },
        1250: {
            slidesPerView: 2,
        }
    }
});

const swiper3 = new Swiper('.usefulSlider', {
    direction: 'horizontal',
    loop: true,

    pagination: {
        el: '.useful-pagination',
        clickable: 'true'
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
            spaceBetween: 20,
        },
        767: {
            slidesPerView: 2,
        },
        1050: {
            slidesPerView: 3,
            spaceBetween: 25,
        }
    }
});

const swiper4 = new Swiper('.howSlider', {
    direction: 'horizontal',
    loop: true,
    slidesPerView: 1,
    pagination: {
        el: '.how-pagination',
        clickable: 'true'
    },
});

const swiper5 = new Swiper('.perksSlider', {
    direction: 'horizontal',
    loop: true,
    slidesPerView: 1,
    pagination: {
        el: '.perks-pagination',
        clickable: 'true'
    },
});