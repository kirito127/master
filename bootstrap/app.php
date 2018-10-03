<?php
/**
 *  Slim Application setting
 *  and bootstrapping
 */
// Require composer autoloader
require __DIR__ . '/../vendor/autoload.php';
session_start();
// Application settings
$settings = require __DIR__ . '/../app/settings.php';

// New Slim app instance
$app = new Slim\App( $settings );

// Add our dependencies to the container
require_once __DIR__ . '/../app/dependencies.php';

// Require our route
require_once __DIR__ . '/../app/routes.php';

//WooApi settings;
// $woocommerce = require __DIR__ . '/../app/Wooapi/woosettings.php';





