/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './scss/app.scss';

// start the Stimulus application
import './bootstrap';
import 'bootstrap';
import $ from 'jquery';
import autosize from 'autosize'
import Typed from 'typed.js';


$('.custom-file-input').on('change', function(e) {
    let inputFile = e.currentTarget;
    $(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
});

// atosize textarea field
autosize(document.querySelector('textarea'));

let slogan = {
    strings: ['Be,', 'Learn,', 'Connect ...'],
    typeSpeed: 150,
    loop: true,
};

new Typed('.typing_text', slogan);

/*************/