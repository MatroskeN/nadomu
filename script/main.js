const swiper = new Swiper('.serviceSlider', {
    direction: 'horizontal',
    loop: true,

    navigation: {
        nextEl: '.button-next',
        prevEl: '.button-prev',
    },

    breakpoints: {
        320: {
            slidesPerView: 2,
        },
        767: {
            slidesPerView: 3,
        }
    }
});

const swiper1 = new Swiper('.specialistsSlider', {
    direction: 'horizontal',
    loop: true,

    // Navigation arrows
    navigation: {
        nextEl: '.spec-next',
        prevEl: '.spec-prev',
    },

    breakpoints: {
        320: {
            slidesPerView: 1,
        },
        767: {
            slidesPerView: 2,
            spaceBetween: 23,
        },
        1250: {
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
        1250: {
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

let faqs = document.querySelectorAll('.faqItem');

faqs.forEach( element => {
    element.onclick = function () {
        element.classList.toggle('active');
    }
})

let tabs = document.querySelector('.controls');

let tabItems = tabs.querySelectorAll('.item');

tabItems.forEach( element => {
    element.onclick = function () {
        tabItems.forEach( item => {
            item.classList.remove('active');
        })
        element.classList.toggle('active');
    }
})