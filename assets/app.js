const $ = require('jquery');
global.$ = global.jQuery = $;

import 'bootstrap';
import './bootstrap';
import 'select2';
import 'select2/dist/js/i18n/fr';

import './styles/app.scss';

$(function() {
    $('[data-simple-select2]').each(function () {
        $(this).select2();
    });
});
