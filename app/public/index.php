<?php
/**
 * Created by PhpStorm.
 * User: irmantas
 * Date: 17.4.28
 * Time: 22.04
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



require '../vendor/autoload.php';
session_start();
$settings = require '../settings/settings.php';

$app = new \Slim\App(['settings' => $settings]);

/*$app->get('/', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});*/

require '../settings/dependencies.php';

require '../settings/routers.php';




$app->run();