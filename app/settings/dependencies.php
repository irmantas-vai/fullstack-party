<?php
$container = $app->getContainer();

$container['view'] = function ($container) {
    $settings = $container->get('settings')['twig'];
    return new \Slim\Views\Twig($settings['tplPpath'], $settings['settings']);
};

$container['controllerIndex'] = function ($container) {
    return new App\Controller\IndexController($container);
};

$container['controllerGithub'] = function ($container) {
    return new App\Controller\AuthController($container);
};

$container['serviceGithub'] = function ($container) {
    return new App\Service\GithubService($container);
};

$container['serviceIssue'] = function ($container) {
    return new App\Service\IssueService($container);
};
