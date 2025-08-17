import './bootstrap.js';
import $ from 'jquery';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// Expose jQuery globally for libraries that expect window.$ / window.jQuery
// (e.g. legacy plugins). This is safe and helps when mixing with CDN assets.
window.$ = window.jQuery = $;

$(function () {
    console.log('dom loaded')
})
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
