let faqs = document.querySelectorAll('.faqItem');

faqs.forEach(element => {
    element.onclick = function () {
        element.classList.toggle('active');
    }
})

let tabs = document.querySelector('.controls');

let tabItems = tabs.querySelectorAll('.item');

tabItems.forEach(element => {
    element.onclick = function () {
        tabItems.forEach(item => {
            item.classList.remove('active');
        })
        element.classList.toggle('active');
    }
})