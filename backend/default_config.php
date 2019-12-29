<?php
define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

define('GLOBAL_SALT', '00000000');

define('COOKIE_REMEMBERME_UID', 'Astronauth_UID');
define('COOKIE_REMEMBERME_TOKEN', 'Astronauth_Token');

define('REMEMBERME_PERIOD_SESSION', 8 * 60 * 60);
define('REMEMBERME_PERIOD_PERSISTENT', 6 * 30 * 24 * 60 * 60);

define('PASSWORD_MIN_LENGTH', 8); # this is the absolute minimum for a secure password

define('POST_ACCOUNT_IDENTIFIER', 'astro-account-identifier');
define('POST_ACCOUNT_USERNAME', 'astro-account-username');
define('POST_ACCOUNT_EMAIL', 'astro-account-email');
define('POST_ACCOUNT_PASSWORD', 'astro-account-password');
define('POST_REMEMBER_ME', 'astro-remember-me');
?>
