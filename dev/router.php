<?php
// Dev-only front controller for the PHP built-in server.
// Apache uses .htaccess to rewrite all non-file requests to index.php?route=...
// The built-in server ignores .htaccess, so this router reproduces that rule.
//
// Run from the repo root:
//   php -S 0.0.0.0:8000 -t . dev/router.php

$root = realpath($_SERVER['DOCUMENT_ROOT']);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve existing real files (css/js/uploads/images) directly.
if ($path !== '/' && $path !== '') {
    $file = realpath($root . urldecode($path));
    if ($file !== false && is_file($file) && strpos($file, $root) === 0) {
        return false;
    }
}

// Everything else goes through the front controller.
$_GET['route'] = ltrim(urldecode($path), '/');
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $root . '/index.php';
