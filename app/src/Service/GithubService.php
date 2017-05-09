<?php

namespace App\Service;

use Milo\Github\OAuth\Configuration;
use Milo\Github\Storages\SessionStorage;
use Milo\Github\OAuth\Login;
use Milo\Github\Api;
use Milo\Github\Paginator;

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
     * @var Api
     */
    protected $api;
    /**
     * @var Login
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
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->api = new Api;
        $config = new Configuration($this->settings['clientId'], $this->settings['clientSecret'], ['public_repo']);
        $storage = new SessionStorage;
        $this->login = new Login($config, $storage);
        if ($this->login->hasToken()) {
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
        return (bool)$this->api->getToken();
    }

    /**
     * @return Api
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
        if (!empty($headers['link'])) {
            $link = Paginator::parseLink($headers['link'], 'last');
            return Paginator::parsePage($link);
        }
        return 1;
    }
}
