/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
// import 'bootstrap';
import bsCustomFileInput from 'bs-custom-file-input';

// start the Stimulus application
import './bootstrap';

bsCustomFileInput.init();

// require('bootstrap');
//
// const $ = require('jquery');

// import './js/ajax'
// import './js/search'

// // this "modifies" the jquery module: adding behavior to it
// // the bootstrap module doesn't export/return anything
//
//
// // require the JavaScript
// require('bootstrap-star-rating');
// // require 2 CSS files needed
// require('bootstrap-star-rating/css/star-rating.css');
// require('bootstrap-star-rating/themes/krajee-svg/theme.css');