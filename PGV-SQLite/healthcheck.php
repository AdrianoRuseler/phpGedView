<?php
// healthcheck.php
if (file_exists(dirname(__FILE__) . '/config.php')) {
    echo "OK";
    exit(0);
} else {
    echo "CONFIG_MISSING";
    exit(1);
}