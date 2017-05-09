<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\GithubService;
use Slim\Router;

/**
 * Class AuthController
 * @package App\Controller
 */
class AuthController
{

    /**
     * @var Router
     */
    protected $router;
    /**
     * @var GithubService
     */
    protected $githubService;


    /**
     * AuthController constructor.
     * @param GithubService $githubService
     * @param Router $router
     */
    public function __construct(GithubService $githubService, Router $router)
    {
        $this->router = $router;
        $this->githubService = $githubService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return mixed
     */
    public function loginAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        if ($this->githubService->checkAuth() !== false) {
            return $response->withRedirect($this->router->pathFor('issue', ['action' => 'list']));
        }

        $url = $request->getUri()->getBaseUrl() . $this->router->pathFor('authCallback');
        $this->githubService->login($url);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function logoutAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->githubService->logout();
        return $response->withRedirect($this->router->pathFor('home'));
    }


    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function callbackAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        if ($this->githubService->checkAuth() !== false) {
            return $response->withRedirect($this->router->pathFor('authLogin'));
        }
        $this->githubService->storeToken();
        return $response->withRedirect($this->router->pathFor('issue', ['action' => 'list']));
    }
}
