<?php

// SERVER
define('DEV_SERVER', 'http://localhost/initframework');
define('TEST_SERVER', 'http://test.initframework.com');
define('LIVE_SERVER', 'http://test.initframework.com');
define('SERVER', DEV_SERVER);


// APPLICATION
define('APPLICATION', 'InitFramework');
define('APPLICATION_DIR', \dirname(__DIR__) . '/');
// TEMPLATE
define('TEMPLATE_ENGINE','init'); // init coming soon... mirror.js, smarty, twig
define('TEMPLATE_DIR', APPLICATION_DIR . 'public/views/');
// ASSETS
define('ASSETS_PATH', SERVER . '/public/assets/');
// STORAGE PATH
define('STORAGE_PATH', 'storage/public/');
// STORAGE
define('STORAGE_DIR', APPLICATION_DIR . STORAGE_PATH);


// MAINTENANCE
define('MAINTENANCE', true);
define('MAINTENANCE_ALLOWED_IP', [
   '::1','127.0.0.1'
]);


// ERROR
define('ERROR_DISPLAY', false);
define('ERROR_LOG', true);
define('ERROR_LOG_FILE', APPLICATION_DIR . 'storage/logs/error.log');
define('EMAIL_LOG', true);
define('EMAIL_LOG_ADDRESS', 'postmaster@localhost');


// Cache
define('CACHE_ENGINE', 'file'); // db, apc, file, mem or memcached
define('CACHE_EXPIRE', 3600);


// DB
define('DB_DRIVER', 'mysql'); // mysql, pgsql, sqlite, mssql
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'testframework_db');
define('DB_PORT', '3306');
define('DB_PREFIX', 'init_');


// MAIL
define('MAIL_DRIVER', 'mail'); // mail, smtp, sendmail
define('MAIL_SMTP_HOST', '');
define('MAIL_SMTP_PORT', 25);
define('MAIL_SMTP_AUTH', false);
define('MAIL_SMTP_USERNAME', '');
define('MAIL_SMTP_PASSWORD', '');
define('MAIL_SMTP_SECURE', 'none'); // none, tls, ssl
define('MAIL_SMTP_TIMEOUT', 10);


// KEY
define('SECRET_KEY', 'cUpSU1JQRk0yd2N1QStLWjJPblRDQT09OjpNMiLa8Ynxu/4tLAZBRApR');


// AUTHENTICATION
define('AUTH_WEB', 'Session'); // Session, JWT
define('AUTH_API', 'JWT'); // JWT, Basic, Digest, OAuth, OAuth2

// SESSION
define('SESSION_AUTOSTART', false);
define('SESSION_ENGINE', 'file'); // file, db
define('SESSION_DIR', APPLICATION_DIR . '/storage/session/');
define('SESSION_NAME', 'ISESSID');
define('SESSION_LIFETIME', 60); // Minutes
define('REMEMBER_ME_LIFETIME', 60); // Minutes
// BASIC
define('BASIC_REALM', 'Initframework');
// DIGEST
define('DIGEST_REALM', 'Initframework');
// JWT


// LANGUAGE & TIMEZONE
define("TIMEZONE", "UTC");


// CUSTOM
// define your custom configurations...
// define('CONFIG', 'VALUE');