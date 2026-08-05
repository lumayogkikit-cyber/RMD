<?php
// Simple requester to fetch headers for PDF route
$url = 'http://127.0.0.1:8000/scaling/reports/pdf';
$headers = @get_headers($url, 1);
if (! $headers) {
    echo "NO_RESPONSE\n";
    exit(1);
}
print_r($headers);
