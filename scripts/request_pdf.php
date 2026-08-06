<?php
// Simple requester to fetch headers for PDF route
$appUrl = getenv('APP_URL') ?: (getenv('APP_URL') === false ? null : getenv('APP_URL'));
if (empty($appUrl)) {
    $appUrl = 'http://127.0.0.1:8000';
}

$url = rtrim($appUrl, '/') . '/scaling/reports/pdf';
$headers = @get_headers($url, 1);
if (! $headers) {
    echo "NO_RESPONSE for {$url}\n";
    exit(1);
}
print_r($headers);
