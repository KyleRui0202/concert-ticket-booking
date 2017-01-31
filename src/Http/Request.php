<?php

namespace Http;

class Request {

    /**
     * Server parameters (for $_SERVER).
     *
     * @var array
     */
    protected $server;

    /**
     * Query string parameters (for $_GET).
     *
     * @var array
     */
    protected $query;

    /**
     * The parameters inside request body (for $_POST).
     *
     * @var array
     */
    protected $request;

    /**
     * Uploaded files (for $_FILES).
     *
     * @var array
     */
    protected $files;

    /**
     * Cookies (for $_COOKIE).
     *
     * @var array
     */
    protected $cookie;

    /**
     * Constructor.
     *
     * @param array $server
     * @param array $query
     * @param array $request
     * @param array $files
     * @param array $cookies
     * @param string|resource $content
     */
    public function __construct($server = [], $query = [], $request = [], $files = [], $cookies = []) {
        $this->server = $server;
        $this->query = $query;
        $this->request = $request;
        $this->files = $files;
        $this->cookies = $cookies;
    }

    /**
     * Get the current HTTP request method.
     *
     * @return string
     */
    public function getMethod() {
        if (isset($this->request['_method'])) {
            return strtoupper($this->request['_method']);
        } else {
            return $this->server['REQUEST_METHOD'];
        }
    }

    /**
     * Get the current HTTP path info.
     *
     * @return string
     */
    public function getPathInfo() {
        return parse_url($this->server['REQUEST_URI'], PHP_URL_PATH);
    }
 
    /**
     * Retrieve the input data from 'POST'.
     *
     * @param array|string|null $name
     * @param mixed default
     * @return default
     */
    public function getPostInput($name = null, $default = null) {   
        if (is_null($name)) {
            return $this->request;
        } elseif (is_array($name)) {
            $values = [];
            foreach ($name as $key) {
                $values[$key] = $this->getPostInput($key, $default);
            }

            return $values;
        } else {
            return isset($this->request[$name]) ? $this->request[$name] : $default;
        }
    }

    /**
     * Retrieve the input data from 'GET'.
     *
     * @param array|string|null $name
     * @param mixed $default
     * @return mixed
     */
    public function getQueryInput($name = null, $default = null) {   
        if (is_null($name)) {
             return $this->query;
        } elseif (is_array($name)) {
            $values = [];
            foreach ($name as $key) {
                $values[$key] = $this->getQueryInput($key, $default);
            }

            return $values;
        } else {
            return isset($this->query[$name]) ? $this->query['name'] : $default;
        }
    }

    /**
     * Check if the request method is of specific type.
     *
     * @param string $method
     * @return bool
     */
    public function isMethod($method) { 
        return $this->getMethod() === strtoupper($method);
    }
}
