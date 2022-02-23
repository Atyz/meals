const $ = require('jquery');
global.$ = global.jQuery = $;

import { Tooltip } from 'bootstrap';
import './bootstrap';
import 'select2';
import 'select2/dist/js/i18n/fr';

import './styles/app.scss';

import { Modal } from './components/Modal.js';

$(function() {
    new Modal();
    $('[data-simple-select2]').each(function () {
        $(this).select2();
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new Tooltip(tooltipTriggerEl)
    })
});
