<?php

namespace Routing;

class RouteRegister {

    /**
     * The instance of the singleton
     *
     * @var self
     */
    private static $instance = null;

    /**
     * All of the registered routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * All of the named routes and URI pairs.
     *
     * @var array
     */
    public $namedRoutes = [];

    /**
     * Private constructor.
     *
     */
    private function __construct() { }
         
    /**
     * Prevent obeject coloning.
     *
     */
    private function __clone() { }

    /**
     * Prevent object unserialization.
     *
     */
    private function __wakeup() { }

    /**
     * Get the repository instance via lazy initialization 
     *
     * @return self
     */
    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new static();
        }
         
        return static::$instance;
    }

    /**
     * Register a route with the application.
     *
     * @param string $uri
     * @param mixed $action
     * @return $this
     */
    public function get($uri, $action) {
        $this->matchRoute('GET', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param string $uri
     * @param mixed $action
     * @return $this
     */
    public function post($uri, $action) {
        $this->matchRoute('POST', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param string $uri
     * @param mixed $action
     * @return $this
     */
    public function put($uri, $action) {
        $this->matchRoute('PUT', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param string $uri
     * @param mixed $action
     * @return $this
     */
    public function patch($uri, $action)
    {
        $this->matchRoute('PATCH', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param string $uri
     * @param mixed $action
     * @return $this
     */
    public function delete($uri, $action) {
        $this->matchRoute('DELETE', $uri, $action);

        return $this;
    }

    /**
     * Add a route to the collection.
     *
     * @param  array|string  $method
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    public function matchRoute($method, $uri, $action) {
        $action = $this->parseAction($action);

        $uri = '/'.trim($uri, '/');

        if (isset($action['as'])) {
            $this->namedRoutes[$action['as']] = $uri;
        }

        if (is_array($method)) {
            foreach ($method as $verb) {
                $this->routes[$verb.$uri] = ['method' => $verb, 'uri' => $uri, 'action' => $action];
            }
        } else {
            $this->routes[$method.$uri] = ['method' => $method, 'uri' => $uri, 'action' => $action];
        }
    }

    /**
     * Parse the action into an array format.
     *
     * @param  mixed  $action
     * @return array
     */
    protected function parseAction($action) {
        if (is_string($action)) {
            return ['uses' => $action];
        } elseif (! is_array($action)) {
            return [$action];
        }

        return $action;
    }

    /**
     * Determine if the route is registered.
     *
     * @param string $method
     * @param string $uri
     * @return bool
     */
    public function isRegistered($method, $uri) {
        $uri = '/'.trim($uri, '/');

        return isset($this->routes[$method.$uri]);
    }

    /**
     * Get the registred view/function for a registered route.
     *
     * @param string $method
     * @param string $uri
     * @return void
     */
    public function resolve($method, $uri) {
        $uri = '/'.trim($uri, '/');

        $action = $this->routes[$method.$uri]['action'];
        if (isset($action['uses'])) {
            require $action['uses'];
        }
        else {
            array_map('call_user_func', $action);
        }
    }
    
    /**
     * Handle dynamic static calls to the object.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($method, $args) {
        $instance = static::getInstance();

        return call_user_func_array([$instance, $method], $args);
    }
}
