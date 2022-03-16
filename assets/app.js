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

    var counter = 0;
    var singleClickTimer;

    $('[data-free]').on('click', function(event) {
        event.preventDefault();
        counter++;

        if (counter === 1) {
            let href = $(this).attr('href');
            singleClickTimer = setTimeout(function() {
                counter = 0;
                return location.href = href;
            }, 300);
        } else if (counter === 2) {
            clearTimeout(singleClickTimer);
            counter = 0;
            return location.href = $(this).data('free');
        }
    });

    $('[data-simple-select2]').each(function () {
        $(this).select2();
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new Tooltip(tooltipTriggerEl)
    })
});
