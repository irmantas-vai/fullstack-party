<?php
/**
 * Created by PhpStorm.
 * User: irmantas
 * Date: 17.4.28
 * Time: 22.04
 */
use Slim\App;
require '../vendor/autoload.php';

session_start();

$settings = require '../settings/settings.php';

$app = new App(['settings' => $settings]);

require '../settings/dependencies.php';

require '../settings/routers.php';

$app->run();
