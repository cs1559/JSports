// media/js/phone-formatter.js
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // Get selector from Joomla script options (passed from PHP)
        const options = Joomla.getOptions('com_jsports.phone') || {};
        const selector = options.selector || 'input[type="tel"]';  // fallback

        const phoneInput = document.querySelector(selector);

        if (!phoneInput) {
            console.warn('Phone input not found for selector: ' + selector);
            return;
        }

        phoneInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.substring(0, 10);

            let formatted = '';
			if (value.length > 0) {
			    formatted = '' + value.substring(0, 3);
			}
			if (value.length >= 3) {
			    formatted += '-' + value.substring(3, 6);
			}
			if (value.length >= 7) {
			    formatted += '-' + value.substring(6, 10);
			}

//            if (value.length > 0) {
//                formatted = '(' + value.substring(0, 3);
//            }
//            if (value.length >= 4) {
//                formatted += ') ' + value.substring(3, 6);
//            }
 //           if (value.length >= 7) {
 //               formatted += '-' + value.substring(6, 10);
 //           }

            e.target.value = formatted;
        });

        // Block non-numeric keys (except controls)
        phoneInput.addEventListener('keydown', function (e) {
            const allowed = [46, 8, 9, 27, 13, 37, 38, 39, 40];
            if (allowed.includes(e.keyCode) ||
                (e.keyCode === 65 && (e.ctrlKey || e.metaKey)) ||
                (e.keyCode === 67 && (e.ctrlKey || e.metaKey)) ||
                (e.keyCode === 86 && (e.ctrlKey || e.metaKey)) ||
                (e.keyCode === 88 && (e.ctrlKey || e.metaKey))) {
                return;
            }
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    });
})();