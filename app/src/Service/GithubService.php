<?php
namespace App\Service;

use Milo\Github;

/**
 * Class GithubService
 * @package App\Service
 */
class GithubService
{
    /**
     * @var
     */
    protected $settings;
    /**
     * @var Github\Api
     */
    protected $api;
    /**
     * @var Github\OAuth\Login
     */
    protected $login;
    /**
     * @var
     */
    protected $response;
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * GithubService constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->settings = $container->get('settings')['github'];
        $this->api = new Github\Api;
        $config = new Github\OAuth\Configuration($this->settings['clientId'], $this->settings['clientSecret'], ['public_repo']);
        $storage = new Github\Storages\SessionStorage;
        $this->login = new Github\OAuth\Login($config, $storage);
        if($this->login->hasToken())
        {
            $this->api->setToken($this->login->getToken());
        }
    }

    /**
     * @param $callbackUrl
     */
    public function login($callbackUrl)
    {
        $this->login->askPermissions($callbackUrl);
    }

    /**
     *
     */
    public function logout()
    {
        $this->login->dropToken();
    }

    /**
     *
     */
    public function storeToken()
    {
        $this->login->obtainToken($_GET['code'], $_GET['state']);
    }

    /**
     * @return bool
     */
    public function checkAuth()
    {
        return (bool) $this->api->getToken();
    }

    /**
     * @return Github\Api
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     *
     */
    public function resetFilters()
    {
        $this->filters = [];
    }

    /**
     * @param $uri
     * @return $this
     */
    public function call($uri)
    {
        $this->response = $this->api->get($uri, $this->filters, ['Content-Type' => 'application/json']);
        $this->resetFilters();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addFilter($key, $value)
    {
        $this->filters[$key] = $value;
        return $this;
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function addFilters(array $filters)
    {
        $this->filters = array_merge($this->filters, $filters);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->api->decode($this->response);
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        $headers = $this->getHeaders();
        if(!empty($headers['link']))
        {
            $link = Github\Paginator::parseLink($headers['link'], 'last');
            return Github\Paginator::parsePage($link);
        }
        return 1;
    }
}