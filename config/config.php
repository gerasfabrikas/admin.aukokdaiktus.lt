<?php

/**
 *
 * Private config file is not versioned.
 * Public config file is versioned.
 *
 * Private config file mirrors public config file.
 * However, private config file holds server specific settings, e.g. passwords of the current server and other sensitive
 * not sharable information.
 *
 * Loading public config file should be enough for dev environment to
 *
 */

$configFilePrivate  = __DIR__ . DIRECTORY_SEPARATOR . 'config.private.php';
$configFilePublic   = __DIR__ . DIRECTORY_SEPARATOR . 'config.public.php';

if(is_file($configFilePrivate)) {
    require_once $configFilePrivate;
} else {
    require_once $configFilePublic;
}