<?php
$postdata = http_build_query(['slip_id' => 6]);
$opts = ['http' => [
    'method'  => 'POST',
    'header'  => "Content-type: application/x-www-form-urlencoded\r\n" .
                 "X-Requested-With: XMLHttpRequest\r\n",
    'content' => $postdata
]];
$context  = stream_context_create($opts);

// You have to log in via curl first to get a session cookie
$ch = curl_init('http://localhost/moizcare/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'username' => 'admin',
    'password' => 'admin123'
]));
$response = curl_exec($ch);
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
$cookies = array();
foreach($matches[1] as $item) {
    parse_str($item, $cookie);
    $cookies = array_merge($cookies, $cookie);
}
curl_close($ch);

$cookie_str = '';
foreach($cookies as $k => $v) { $cookie_str .= "$k=$v; "; }

$ch = curl_init('http://localhost/moizcare/payroll/send_email_single');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['slip_id' => 6]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Requested-With: XMLHttpRequest",
    "Cookie: $cookie_str"
]);
$result = curl_exec($ch);
echo "RESPONSE FROM AJAX POST:\n";
echo $result;
