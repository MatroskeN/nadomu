const swiper = new Swiper('.serviceSlider', {
    direction: 'horizontal',
    loop: true,
    slidesPerView: 3,

    navigation: {
        nextEl: '.button-next',
        prevEl: '.button-prev',
    },
});

const swiper1 = new Swiper('.specialistsSlider', {
    direction: 'horizontal',
    loop: true,
    slidesPerView: 4,

    // Navigation arrows
    navigation: {
        nextEl: '.spec-next',
        prevEl: '.spec-prev',
    },
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
        el: '.swiper-pagination',
        clickable: 'true'
    }
});

let faqs = document.querySelectorAll('.faqItem');

faqs.forEach( element => {
    element.querySelector('.open').onclick = function () {
        element.classList.toggle('active');
    }
})