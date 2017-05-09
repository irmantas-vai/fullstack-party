<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\GithubService;
use App\Service\IssueService;
use Slim\Views\Twig;
use Slim\Router;

/**
 * Class IssueController
 * @package App\Controller
 */
class IssueController
{

    /**
     * @var Twig
     */
    protected $view;
    /**
     * @var IssueService
     */
    protected $issueService;
    /**
     * @var GithubService
     */
    protected $githubService;
    /**
     * @var Router
     */
    protected $router;

    /**
     * IssueController constructor.
     * @param Twig $view
     * @param IssueService $issueService
     * @param GithubService $githubService
     * @param Router $router
     */
    public function __construct(Twig $view, GithubService $githubService, IssueService $issueService, Router $router)
    {
        $this->view = $view;
        $this->issueService = $issueService;
        $this->githubService = $githubService;
        $this->router = $router;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    protected function listAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        if (($page = (int)$request->getAttribute('args')) === 0) {
            $page++;
        }
        $this->issueService->getTotalIssues('open');
        $tplData = [
            'openIssues' => $this->issueService->getTotalIssues('open'),
            'closedIssues' => $this->issueService->getTotalIssues('closed'),
            'list' => $this->issueService->getIssues($page),
            'totalPages' => $this->issueService->getTotalPages($page),
            'currentPage' => $page,
            'issueUri' => $this->router->pathFor('issue', ['action' => 'item']),
            'issuesUri' => $this->router->pathFor('issue', ['action' => 'list']),
        ];
        return $this->view->render($response, 'issue/list.twig', $tplData);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    protected function itemAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $path = $request->getAttribute('args');
        $issue = $this->issueService->getIssue($path);
        $comments = [];
        if ($issue->comments) {
            $comments = $this->issueService->getIssueComments($path);
        }
        $tplData = [
            'issue' => $issue,
            'comments' => $comments,
        ];

        return $this->view->render($response, 'issue/item.twig', $tplData);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args)
    {

        if ($this->githubService->checkAuth() !== true) {
            return $response->withRedirect($this->router->pathFor('home'));
        }
        $action = $request->getAttribute('action');
        if (method_exists($this, "{$action}Action")) {
            return $this->{"{$action}Action"}($request, $response, $args);
        }
    }
}
