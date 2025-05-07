<?php
// config/config.php

define('DB_HOST', 'db5017341063.hosting-data.io');
define('DB_NAME', 'dbs13906277');
define('DB_USER', 'dbu1346818');
define('DB_PASS', 'AngryBear41*/');
define('DB_DSN', 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// identifiants admin
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'secret123'); // changez ici !
// adresse “From” qui sera utilisée pour tous les mails
define('MAIL_FROM_ADDRESS', 'paul.barbara@protonmail.com');
define('MAIL_FROM_NAME',    'Culture & Data');

// adresse de l’administrateur qui reçoit la notification
define('ADMIN_EMAIL',       'paul.barbara@protonmail.com');

// URL de base de votre application (pour générer les liens)
define('BASE_URL',          'https://test.el-editor.online/index.php');