document.querySelector('.burger').onclick = function () {
    document.querySelector('.burgerMenu').classList.add('activeBurger');
}

document.querySelector('.burgerMenu').onclick = function (e) {
    closeSandwichMenu()
}

document.querySelector('.close').onclick = function () {
    closeSandwichMenu()
}

function closeSandwichMenu() {
    document.querySelector('.burgerMenu').classList.remove('activeBurger');
}




