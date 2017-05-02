<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuthController
 * @package App\Controller
 */
class AuthController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * AuthController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function loginAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $githubService = $this->container->get('serviceGithub');
        if($githubService->checkAuth() !== false)
        {
            return $response->withRedirect($this->container->get('router')->pathFor('issue', ['action' => 'list']));
        }
        $githubService->login($request->getUri()->getBaseUrl() . $this->container->get('router')->pathFor('authCallback'));
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function logoutAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->container->get('serviceGithub')->logout();
        return $response->withRedirect($this->container->get('router')->pathFor('home'));
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function callbackAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $githubService = $this->container->get('serviceGithub');
        if($githubService->checkAuth() !== false)
        {
            return $response->withRedirect($this->container->get('router')->pathFor('authLogin'));
        }
        $githubService->storeToken();
        return $response->withRedirect($this->container->get('router')->pathFor('issue', ['action' => 'list']));
    }
}