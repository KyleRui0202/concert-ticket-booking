<?php

use Configuration\Repository as ConfigRepository;
use Routing\RouteRegister;

if (! function_exists('array_has')) {
    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param array $array
     * @param string|null $key
     * @return bool
     */
    function array_has($array, $key) {
        if (empty($array) || is_null($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }

            $array = $array[$segment];
        }
        return true;
    }
}

if (! function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array $array
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null) {
        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }
        return $array;
    }
}

if (! function_exists('array_set')) {
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If a null key is given, the entire array will be replaced.
     *
     * @param array $array
     * @param string|null $key
     * @param mixed $value
     * @return void
     */
    function array_set(&$array, $key, $value) {
        if (is_null($key)) {
            $array = $value;
            return;
        }

        $keys = explode('.', $key);
        $numKeys = count($keys);

        $count = 0;
        foreach ($keys as $key) {
            if (++$count !== $numKeys) {
                if (!array_key_exists($key, $array) || !is_array($array[$key])) {
                    $array[$key] = [];
                }

                $array = &$array[$key];
            }
            else {
                $array[$key] = $value;
            }
        }
    }
}

if (! function_exists('base_path')) {
    /**
     * Get the absolute path from relative application path.
     *
     * @param  string  $path
     * @return string
     */
    function base_path($path = '')
    {
        return (ConfigRepository::getInstance()->getBasePath() ?: realpath(__DIR__.'/..')).
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('datastore_path')) {
    /**
     * Get the absolute path from relative "datastore" path .
     *
     * @param  string  $path
     * @return string
     */
    function datastore_path($path = '')
    {
        return base_path('datastore').
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('public_path')) {
    /**
     * Get the absolute path from relative "public" path.
     *
     * @param  string  $path
     * @return string
     */
    function public_path($path = '') {
        return public_path('datastore').
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('resource_path')) {
    /**
     * Get the absolute path from relative "resources" path.
     *
     * @param  string  $path
     * @return string
     */
    function resource_path($path = '') {
        return base_path('resources').
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('view_path')) {
    /**
     * Get the absolute path from relative "views" path.
     *
     * @param  string  $path
     * @return string
     */
    function view_path($path = '') {
        return resource_path('views').
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('config')) {
    /**
     * Get/set the specified configuration value(s).
     *
     * If an array is passed as the key, it will set an array of configuration values.
     *
     * @param array|string|null $key
     * @param mixed  $default
     * @return mixed
     */
    function config($key = null, $default = null) {
        if (is_null($key)) {
            return ConfigRepository::getInstance();
        }

        if (is_array($key)) {
            return ConfigRepository::getInstance()->set($key);
        }

        return ConfigRepository::getInstance()->get($key, $default);
    }
}

if (! function_exists('route_register')) {
    /**
     * Resolve/register the specified route(s).
     *
     * If an action is given, it will register the specified method(s) and
     * URI with the action. Otherwise, it will try to resolve them.
     *
     * @param  array|null $methodAndUri
     * @param  mixed  $action
     * @return mixed
     */
    function route_register($methodAndUri = null, $action = null) {
        if (is_null($methodAndUri)) {
            return RouteRegister::getInstance();
        }

        if (is_null($action)) {
            if (RouteRegister::getInstance()->isRegistered(
                $method = $methodAndUri['method'], $uri = $methodAndUri['uri'])) {
                RouteRegister::getInstance()->resolve($method, $uri);
            } else {
                throw new InvalidArgumentException("Route Not Rgistered: [$method: $uri]");
            }
        }
        else {
            RouteRegister::getInstance()->matchRoute(
                $methodAndUri['method'], $methodAndUri['uri'], $action);
        }
    }
}

if (! function_exists('session')) {
    /**
     * Get/set/delete the specified session value(s).
     *
     * Only the item at the top level can be deleted by
     * setting its value to null through this function 
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function session($key = null, $default = null) {
        if (is_null($key)) {
            return isset($_SESSION) ? $_SESSION : null;
        }

        if (is_array($key)) {
            foreach ($key as $asscKey => $asscValue) {
                if (is_null($asscValue) && strpos($asscKey, '.') === false) {
                    unset($_SESSION[$asscKey]);
                }
                else { 
                    array_set($_SESSION, $asscKey, $asscValue);
                }
            }

            return;
        }

        return isset($_SESSION) ? array_get($_SESSION, $key, $default) : null;
    }
}

if (! function_exists('url')) {
    /**
     * Generate a url for the specific path.
     *
     * @param string $path
     * @param array $parameters
     * @param bool|null $secure
     * @return string
     */
    function url($path, $parameters = [], $secure = null) {
        // Check if the path itself is a valid URL
        if (filter_var($path, FILTER_VALIDATE_URL, ['flags' => [
            FILTER_FLAG_SCHEME_REQUIRED, FILTER_FLAG_HOST_REQUIRED]]) !== false) {
            return $path;
        }

        // Get the scheme
        if (is_null($secure)) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                $_SERVER['SERVER_PORT'] == '443';
        }
        $scheme = $secure ? 'https://' : 'http://';

        // Get the port
        $port = $_SERVER['SERVER_PORT'];
        $port = (!$secure && $port == '80') || ($secure && $port == '443') ? '' : ':'.$port;

        // Get the host
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'].$port;

        // Get the path and old query
        if (($queryPos = strpos($path, '?')) !== false) {
            list($path, $query) = [substr($path, 0, $queryPos), substr($path, $queryPos+1)];
        }
        else {
            list($path, $query) = [$path, ''];
        }

        // Get the URL without query
        $path = trim($path, '/');
        $url = $scheme . $host . ($path ? '/'.$path : '');

        // Insert the input parameters into the query
        $parameters = array_map(null, array_keys($parameters), array_values($parameters));
        $parameters = array_map(function ($parameter) {
            return $parameter[0] . '=' . $parameter[1];  
        }, $parameters);
        $extraQuery = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
        $query .= $extraQuery;

        return $url . ($query ? '?'.$query : ''); 
    }
}
