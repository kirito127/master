<?php
/**
 *  Slim Application setting
 *  and bootstrapping
 */
// Require composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Application settings
$settings = require __DIR__ . '/../app/settings.php';

// New Slim app instance
$app = new Slim\App( $settings );

//database functions
require_once __DIR__ . '/../db/query.php';

// Add our dependencies to the container
require_once __DIR__ . '/../app/dependencies.php';

// Require our route
require_once __DIR__ . '/../app/routes.php';

//WooApi settings;
// $woocommerce = require __DIR__ . '/../app/Wooapi/woosettings.php';





