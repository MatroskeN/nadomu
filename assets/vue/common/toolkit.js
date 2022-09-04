const ToolkitService = {
    formatPhoneNumber: function (phoneNumberString) {
        let cleaned = ('' + phoneNumberString).replace(/\D/g, '')
        let match = cleaned.match(/^(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})$/)
        if (match) {
            return '+' + match[1] + ' (' + match[2] + ') ' + match[3] + '-' + match[4] + '-' + match[5]
        }
        return null
    },
    scrollToElement: function (element_id) {
        let element = document.getElementById(element_id);

        if (element)
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
    },
    scrollElementByError: function (values) {
        let keys = Object.keys(values);
        if (typeof keys === 'object' && keys.length > 0) {
            let id_element = keys[0];

            this.scrollToElement(id_element);
        }
    },
    wrongKeyboardLanguage: function (string) {
        let replacer = {
            "q":"й", "w":"ц", "e":"у", "r":"к", "t":"е", "y":"н", "u":"г",
            "i":"ш", "o":"щ", "p":"з", "[":"х", "]":"ъ", "a":"ф", "s":"ы",
            "d":"в", "f":"а", "g":"п", "h":"р", "j":"о", "k":"л", "l":"д",
            ";":"ж", "'":"э", "z":"я", "x":"ч", "c":"с", "v":"м", "b":"и",
            "n":"т", "m":"ь", ",":"б", ".":"ю", "/":"."
        };

        return string.replace(/[A-z/,.;\'\]\[]/g, function ( x ){
            return x === x.toLowerCase() ? replacer[ x ] : replacer[ x.toLowerCase() ].toUpperCase();
        });
    },
    dropElementFromArrayByValue: function(array, search) {
        var index = array.indexOf(search);
        if (index !== -1) {
            array.splice(index, 1);
        }

        return array;
    },
    plural: function(number, one, two, five) {
        number = Math.abs(number);
        number %= 100;
        if (number >= 5 && number <= 20) {
            return number + ' ' + five;
        }
        number %= 10;
        if (number === 1) {
            return number + ' ' + one;
        }
        if (number >= 2 && number <= 4) {
            return number + ' ' + two;
        }
        return number + ' ' + five;
    }
}

export default ToolkitService;
