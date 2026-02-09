<?php

$src = '/etc/secrets/isrgrootx1.pem';
$dst = __DIR__ . '/../storage/certs/isrgrootx1.pem';

if (! file_exists($dst)) {
    if (! is_readable($src)) {
        throw new RuntimeException('Secret CA file is not readable');
    }

    if (! is_dir(dirname($dst))) {
        mkdir(dirname($dst), 0755, true);
    }

    copy($src, $dst);
    chmod($dst, 0644);
}
