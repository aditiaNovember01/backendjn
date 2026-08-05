<?php
// Test login endpoint via HTTP
$url = 'http://jayanusabackend.test/api/v1/login';
$data = ['nobp' => '2210050', 'password' => '01112002'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

echo "=== TEST LOGIN API ===\n";
echo "URL: $url\n";
echo "Payload: " . json_encode($data) . "\n";
echo "HTTP Code: $httpCode\n";
if ($error) echo "cURL Error: $error\n";
echo "Response:\n$response\n";

// Juga test dengan field name 'nim' kalau flutter pakai itu
echo "\n=== TEST DENGAN FIELD 'nim' ===\n";
$data2 = ['nim' => '2210050', 'password' => '01112002'];
$ch2 = curl_init($url);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data2));
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
$resp2 = curl_exec($ch2);
$code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);
echo "HTTP Code: $code2\nResponse: $resp2\n";
