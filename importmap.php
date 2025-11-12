<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    '@swup/fade-theme' => [
        'version' => '1.0.5',
    ],
    '@swup/slide-theme' => [
        'version' => '1.0.5',
    ],
    '@swup/forms-plugin' => [
        'version' => '2.0.1',
    ],
    '@swup/plugin' => [
        'version' => '2.0.2',
    ],
    'swup' => [
        'version' => '3.1.1',
    ],
    'delegate-it' => [
        'version' => '6.0.1',
    ],
    '@swup/debug-plugin' => [
        'version' => '3.0.0',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'datatables.net' => [
        'version' => '2.3.4',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap' => [
        'version' => '5.3.3',
    ],
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'datatables' => [
        'path' => './assets/datatables.js',
        'entrypoint' => true,
    ],
    'bootstrap-init' => [
        'path' => './assets/bootstrap.js',
        'entrypoint' => true,
    ],
    'datatables.net-bs5' => [
        'version' => '2.3.4',
    ],
    'datatables.net-bs5/css/dataTables.bootstrap5.min.css' => [
        'version' => '2.3.4',
        'type' => 'css',
    ],
    'datatables.net-responsive-bs5' => [
        'version' => '3.0.7',
    ],
    'datatables.net-responsive' => [
        'version' => '3.0.7',
    ],
    'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css' => [
        'version' => '3.0.7',
        'type' => 'css',
    ],
];
