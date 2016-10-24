<?php

use Zend\Mvc\Application;

header('Content-Type:text/html;charset=utf-8');

date_default_timezone_set('Asia/Shanghai');

if (PHP_VERSION < '5.5.0') {
    exit('PHP Version is to Low!');
}

error_reporting(E_ALL);

ini_set('display_errors', 1);

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
ini_set('user_agent', 'ZFTool - Zend Framework 2 command line tool');

// load autoloader
if (file_exists( __DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (\Phar::running()) {
    require_once __DIR__ . '/vendor/autoload.php';
}else {
    echo 'Error: I cannot find the autoloader of the application.' . PHP_EOL;
    echo "Check if __DIR__ contains a valid ZF2 application." . PHP_EOL;
    exit(2);
}

// Retrieve configuration
if (file_exists(__DIR__ . '/config/application.config.php')) {
    $appConfig = require __DIR__ . '/config/application.config.php';
    if (!isset($appConfig['modules']['CmfTool'])) {
        $appConfig['modules'][] = 'CmfTool';
        $appConfig['module_listener_options']['module_paths']['CmfTool'] = __DIR__;
    }
} else {
    $appConfig = [
        'modules' => [
            'CmfTool',
            'Zend\Router',
            'Zend\Mvc\Console',
        ],
        'module_listener_options' => [
            'config_glob_paths' => [
                'config/autoload/{,*.}{global,local}.php',
            ],
            'module_paths' => [
                '.',
                './vendor'
            ]
        ]
    ];
}


// Run the application!
Application::init($appConfig)->run();