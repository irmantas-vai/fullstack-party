<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class IssueController
 * @package App\Controller
 */
class IssueController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * IssueController constructor.
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
    protected function _listAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        if(($page = (int) $request->getAttribute('args')) === 0)
        {
            $page++;
        }
        $this->container->get('serviceIssue')->getTotalIssues('open');
        $tplData = [
            'openIssues' => $this->container->get('serviceIssue')->getTotalIssues('open'),
            'closedIssues' => $this->container->get('serviceIssue')->getTotalIssues('closed'),
            'list' => $this->container->get('serviceIssue')->getIssues($page),
            'totalPages' => $this->container->get('serviceIssue')->getTotalPages($page),
            'currentPage' => $page,
            'issueUri' => $this->container->get('router')->pathFor('issue', ['action' => 'item']),
            'issuesUri' => $this->container->get('router')->pathFor('issue', ['action' => 'list']),
        ];
        return $this->container->get('view')->render($response, 'issue/list.twig', $tplData);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    protected function _itemAction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $path = $request->getAttribute('args');
        $issue = $this->container->get('serviceIssue')->getIssue($path);
        $comments = [];
        if($issue->comments)
        {
            $comments = $this->container->get('serviceIssue')->getIssueComments($path);
        }
        $tplData = [
            'issue' => $issue,
            'comments' => $comments,
        ];
        return $this->container->get('view')->render($response, 'issue/item.twig', $tplData);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $githubService = $this->container->get('serviceGithub');
        if($githubService->checkAuth() !== true)
        {
            return $response->withRedirect($this->container->get('router')->pathFor('home'));
        }
        $action = $request->getAttribute('action');
        if(method_exists($this, "_{$action}Action"))
        {
            return $this->{"_{$action}Action"}($request, $response, $args);
        }
    }
}