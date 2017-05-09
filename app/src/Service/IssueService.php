<?php

namespace App\Service;

use Westsworld\TimeAgo;

/**
 * Class IssueService
 * @package App\Service
 */
class IssueService
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @var
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $user;

    /**
     * @var GithubService
     */
    protected $githubService;


    /**
     * IssueService constructor.
     * @param GithubService $githubService
     * @param array $settings
     */
    public function __construct(GithubService $githubService, array $settings)
    {
        $this->settings = $settings;
        $this->githubService = $githubService;
        $this->user = $this->getUser();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->githubService->call('/user')->getData();
    }

    /**
     * @param $page
     * @return mixed
     */
    public function getIssues($page)
    {
        $filters = [
            'state' => 'all',
            'sort' => 'updated',
            'direction' => 'desc',
            'page' => $page,
            'per_page' => $this->settings['issuesPerPage'],
        ];
        $this->githubService->addFilters($filters);
        $issues = $this->githubService->call('/issues')->getData();
        foreach ($issues as &$issue) {
            $issue->opened = $this->calcTime($issue->created_at);
            $issue->path = str_replace('https://api.github.com/', '', $issue->url);
        }
        return $issues;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getIssue($path)
    {
        $issue = $this->githubService->call("/{$path}")->getData();
        $issue->opened = $this->calcTime($issue->created_at);
        return $issue;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getIssueComments($path)
    {
        $comments = $this->githubService->call("/{$path}/comments")->getData();
        foreach ($comments as &$comment) {
            $comment->opened = $this->calcTime($comment->created_at);
        }
        return $comments;
    }

    /**
     * @param $state
     * @return mixed
     */
    public function getTotalIssues($state)
    {
        $data = $this->githubService
            ->call("/search/issues?q=type:issue+state:{$state}+user:{$this->user->login}")
            ->getData();
        return $data->total_count;
    }

    /**
     * @param $current
     * @return mixed
     */
    public function getTotalPages($current)
    {
        if (($total = $this->githubService->getTotalPages()) < $current) {
            return $current;
        }
        return $total;
    }

    /**
     * @param $time
     * @return string
     */
    protected function calcTime($time)
    {
        $timeAgo = new TimeAgo();
        return $timeAgo->inWords($time);
    }
}
