<?php

$app->get('/', App\Controller\IndexController::class . ':indexAction')->setName('home');
$app->get('/auth/login', App\Controller\AuthController::class . ':loginAction')->setName('authLogin');
$app->get('/auth/logout', App\Controller\AuthController::class . ':logoutAction')->setName('authLogout');
$app->get('/auth/callback', App\Controller\AuthController::class . ':callbackAction')->setName('authCallback');
$app->get('/issue/{action}/[{args:.*}]', App\Controller\IssueController::class)->setName('issue');
