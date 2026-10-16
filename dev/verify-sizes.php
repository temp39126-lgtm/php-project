<?php
/**
 * CLI: verify admin size settings update storefront CSS variables.
 * Usage: php dev/verify-sizes.php
 */
require __DIR__ . '/../config.php';

function fail($msg) {
    fwrite(STDERR, "FAIL: $msg\n");
    exit(1);
}

function ok($msg) {
    echo "PASS: $msg\n";
}

function root_vars_from_html($html) {
    if (!preg_match('/:root\{([^}]+)\}/', $html, $m)) {
        return null;
    }
    $out = [];
    foreach (explode(';', $m[1]) as $part) {
        $part = trim($part);
        if ($part === '' || strpos($part, ':') === false) {
            continue;
        }
        [$k, $v] = explode(':', $part, 2);
        $out[trim($k)] = trim($v);
    }
    return $out;
}

function fetch_home_root() {
    $ctx = stream_context_create(['http' => ['header' => "Cookie: " . ($GLOBALS['_verify_cookie'] ?? '') . "\r\n"]]);
    $html = @file_get_contents('http://127.0.0.1:8000/', false, $ctx);
    if ($html === false) {
        fail('Could not fetch homepage — is php -S running on :8000?');
    }
    return root_vars_from_html($html);
}

function admin_login() {
    $ch = curl_init('http://127.0.0.1:8000/admin/');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'admin_login=1&username=admin&password=admin123',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER => true,
        CURLOPT_COOKIEJAR => '/tmp/verify-sizes-cookies.txt',
        CURLOPT_COOKIEFILE => '/tmp/verify-sizes-cookies.txt',
    ]);
    curl_exec($ch);
    curl_close($ch);
    $GLOBALS['_verify_cookie'] = '';
    if (is_readable('/tmp/verify-sizes-cookies.txt')) {
        $lines = file('/tmp/verify-sizes-cookies.txt', FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            if ($line !== '' && $line[0] !== '#') {
                $parts = explode("\t", $line);
                if (count($parts) >= 7) {
                    $GLOBALS['_verify_cookie'] .= $parts[5] . '=' . $parts[6] . '; ';
                }
            }
        }
    }
}

function admin_save($page, array $settings) {
    $data = ['action' => 'save_settings', 'return' => $page];
    foreach ($settings as $k => $v) {
        $data["s[$k]"] = (string) $v;
    }
    $ch = curl_init("http://127.0.0.1:8000/admin/$page");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEJAR => '/tmp/verify-sizes-cookies.txt',
        CURLOPT_COOKIEFILE => '/tmp/verify-sizes-cookies.txt',
    ]);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code >= 400) {
        fail("admin save $page returned HTTP $code");
    }
    ok("admin save $page HTTP $code");
}

function assert_var($root, $name, $expectPx) {
    $expect = $expectPx . 'px';
    if (!isset($root[$name]) || $root[$name] !== $expect) {
        fail("$name expected $expect got " . ($root[$name] ?? 'missing'));
    }
    ok("$name = $expect on storefront");
}

admin_login();

echo "=== INCREASE ===\n";
admin_save('header', ['logo_size' => 200]);
admin_save('banners', ['slider_size' => 600, 'banner_size' => 450]);
admin_save('icons', ['icon_size' => 90]);
admin_save('posters', ['poster_size' => 120]);
admin_save('usps', ['usp_size' => 70]);
$root = fetch_home_root();
assert_var($root, '--logo-h', 200);
assert_var($root, '--slider-h', 600);
assert_var($root, '--banner-h', 450);
assert_var($root, '--icon-h', 90);
assert_var($root, '--poster-h', 120);
assert_var($root, '--usp-h', 70);

echo "=== DECREASE ===\n";
admin_save('header', ['logo_size' => 40]);
admin_save('banners', ['slider_size' => 250, 'banner_size' => 180]);
admin_save('icons', ['icon_size' => 30]);
admin_save('posters', ['poster_size' => 45]);
admin_save('usps', ['usp_size' => 20]);
$root = fetch_home_root();
assert_var($root, '--logo-h', 40);
assert_var($root, '--slider-h', 250);
assert_var($root, '--banner-h', 180);
assert_var($root, '--icon-h', 30);
assert_var($root, '--poster-h', 45);
assert_var($root, '--usp-h', 20);

echo "=== ALL TESTS PASSED ===\n";
