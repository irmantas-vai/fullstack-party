<?php
use Psr\Container\ContainerInterface;

$container = $app->getContainer();

$container[Slim\Views\Twig::class] = function (ContainerInterface $container) {
    $settings = $container->get('settings')['twig'];
    return new Slim\Views\Twig($settings['tplPpath'], $settings['settings']);
};

$container[App\Controller\IndexController::class] = function (ContainerInterface $container) {
    return new App\Controller\IndexController($container->get(Slim\Views\Twig::class));
};

$container[App\Controller\AuthController::class] = function (ContainerInterface $container) {
    return new App\Controller\AuthController(
        $container->get(App\Service\GithubService::class),
        $container->get('router')
    );
};

$container[App\Controller\IssueController::class] = function (ContainerInterface $container) {
    return new App\Controller\IssueController(
        $container->get(Slim\Views\Twig::class),
        $container->get(App\Service\GithubService::class),
        $container->get(App\Service\IssueService::class),
        $container->get('router')
    );
};

$container[App\Service\GithubService::class] = function (ContainerInterface $container) {
    return new App\Service\GithubService($container->get('settings')['github']);
};

$container[App\Service\IssueService::class] = function (ContainerInterface $container) {
    return new App\Service\IssueService(
        $container->get(App\Service\GithubService::class),
        $container->get('settings')['issue']
    );
};
