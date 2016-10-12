<?php

$ipPath = __DIR__ . '/cache/' . $_SERVER['REMOTE_ADDR'];

if (!file_exists($ipPath)) {
    touch($ipPath);
}

file_put_contents($ipPath, (new DateTime())->format('Y-m-d H:i:s') . "\n", FILE_APPEND);

$lines = 0;
$handle = fopen($ipPath, "r");
while (!feof($handle)) {
    $line = fgets($handle);
    $lines++;
}
fclose($handle);

// limit to 1000 requests an hour
if ($lines > 1000) {
    throw new Exception('Unable to use service due to limit');
}
