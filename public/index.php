<?php
// public/index.php
session_start();
// 1) load config
require_once __DIR__ . '/../config/config.php';

// 2) simple PSR-4-style autoloader for controllers/models
spl_autoload_register(function($class){
    $paths = [__DIR__.'/../app/controllers/', __DIR__.'/../app/models/'];
    foreach ($paths as $p) {
        $f = $p . $class . '.php';
        if (file_exists($f)) {
            require_once $f;
            return;
        }
    }
});

// 3) determine controller & action
$ctrl  = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'ContactController';
$action = isset($_GET['action']) ? $_GET['action'] : 'form';

// 4) dispatch
if (!class_exists($ctrl) || !method_exists($ctrl, $action)) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
}

$controller = new $ctrl();
$controller->$action();
