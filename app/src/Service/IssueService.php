<?php
namespace App\Service;

use Milo\Github;
use Pimple\Container;

/**
 * Class IssueService
 * @package App\Service
 */
class IssueService
{
    /**
     * @var
     */
    protected $settings;
    /**
     * @var
     */
    protected $response;
    /**
     * @var
     */
    protected $user;

    /**
     * IssueService constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->settings = $container->get('settings')['issue'];
        $this->api = $container->get('serviceGithub');
        $this->user = $this->getUser();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->api->call('/user')->getData();
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
        $this->api->addFilters($filters);
        $issues = $this->api->call('/issues')->getData();
        foreach ($issues as &$issue)
        {
            $issue->opened = $this->_calcTime($issue->created_at);
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
        $issue = $this->api->call("/{$path}")->getData();
        $issue->opened = $this->_calcTime($issue->created_at);
        return $issue;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getIssueComments($path)
    {
        $comments = $this->api->call("/{$path}/comments")->getData();
        foreach ($comments as &$comment)
        {
            $comment->opened = $this->_calcTime($comment->created_at);
        }
        return $comments;
    }

    /**
     * @param $state
     * @return mixed
     */
    public function getTotalIssues($state)
    {
        $data = $this->api
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
        if(($total = $this->api->getTotalPages()) < $current)
        {
            return $current;
        }
        return $total;
    }

    /**
     * @param $time
     * @return string
     */
    protected function _calcTime($time)
    {
        $timeAgo = new \Westsworld\TimeAgo();
        return $timeAgo->inWords($time);
    }
}