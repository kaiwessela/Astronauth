<?php
$token = bin2hex(openssl_random_pseudo_bytes(32));
$hash = password_hash($token, PASSWORD_DEFAULT);
echo $token . PHP_EOL;
echo $hash . PHP_EOL;
echo strlen($hash) . PHP_EOL;
?>
