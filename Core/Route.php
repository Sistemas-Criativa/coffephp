<?php

namespace Core;

use Core\Filter;
use Core\Make;
use Core\Request;
use Core\HTTP;
use Config\Config;

class Route
{

	protected static $routes = array();
	private static $route = "";
	private static $tokennized = false;
	private static $name = "";
	private static $prefixController = "";
	private static $controller = "";
	private static $filters = array();

	/**
	 * Create a Route get
	 */
	public final static function Get($route, $controller)
	{
		//define internal vars
		self::$route = $route;
		self::$controller = $controller;
		self::$tokennized = false;
		//save a Route
		self::saveRoute('GET');
		return (new static);
	}
	/**
	 * Match a method to route
	 */
	public final static function Match($route, $controller, array $methods = [])
	{
		//define internal vars
		self::$route = $route;
		self::$controller = $controller;
		self::$tokennized = false;
		//save a Route
		self::saveRoute($methods);
		return (new static);
	}
	/**
	 * Add prefix to route
	 */
	public final static function prefixController(string $prefix, $function)
	{
		self::$prefixController = $prefix;
		return $function(new static);
	}

	/**
	 * Save a route
	 */
	private final static function saveRoute($method)
	{
		array_push((new static)::$routes, array('name' => self::$name, 'route' => self::$route, 'controller' => self::$prefixController . self::$controller, 'method' => (is_array($method) ? $method : strtoupper($method)), 'filters' => self::$filters, 'tokennized' => self::$tokennized));
		self::$name = "";
		self::$filters = array();
	}

	/**
	 * Save a name for a route
	 */
	protected final static function name(string $name)
	{
		if (!empty($name)) {
			self::$name = $name;
		}
		return (new static);
	}

	/**
	 * Save the router filter
	 */
	protected final static function filter(array $filters)
	{
		if (count($filters) > 0) {
			self::$filters = $filters;
		}
		return (new static);
	}
	/**
	 * get a specific route
	 */
	public final static function route(string $name, bool $fullURL = false, array $args = [])
	{
		$return = "";
		$usedargs = 0;

		//Iterate the route list
		for ($i = 0; $i < sizeof(self::$routes); $i++) {
			//verify if the route name is equals array item

			if (self::$routes[$i]['name'] == $name) {
				//explode a route
				$temp = explode("/", self::$routes[$i]['route']);
				//iterate each route item
				for ($j = 0; $j < sizeof($temp); $j++) {
					//verify if item is variable
					if (substr($temp[$j], 0, 1) == "@") {
						$arrayValues = array_values($args);
						//verify if total of args is valid
						if (!isset($arrayValues[$usedargs])) {
							throw new \Exception("Insuficient args", 3);
						} else {
							//substitui pela variável
							$temp[$j] = $arrayValues[$usedargs];
							$usedargs++;
						}
					}
				}

				//join a Route
				$return = implode("/", $temp);
				if ($fullURL)
					$return = Request::server('REQUEST_SCHEME') . '://' . Request::server('HTTP_HOST') . $return;
			}
		}
		return $return;
	}

	/**
	 * Create a post Route
	 */
	public final static function Post($route, $controller, $tokennized = true)
	{
		self::$route = $route;
		self::$controller = $controller;
		self::$tokennized = $tokennized;
		self::saveRoute('POST');
		return (new static);
	}

	/**
	 * Verify if method is allowed
	 */
	private static function verifyMethod($method = "GET")
	{
		if (is_array($method)) {
			$allowed = false;
			foreach ($method as $item) {
				if ($_SERVER['REQUEST_METHOD'] != strtoupper($item)) {
					$allowed = true;
					break;
				}
			}
			return $allowed;
		}
		if ($_SERVER['REQUEST_METHOD'] != strtoupper($method)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Open the route
	 */
	private static function openRoute()
	{
		//get the args
		$request = (string) $_SERVER['REQUEST_URI'];
		if (strpos($_SERVER['REQUEST_URI'], "?")) {
			Request::query($request);
			$request = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "?"));
		}
		$args = explode("/", trim($request));
		$match = false;
		$controller = "";
		$arguments = array();
		$method = "";
		$methods = [];
		$tokennized = false;

		$filter = array();
		//iterate the Routes
		for ($j = 0; $j < sizeof(self::$routes); $j++) {
			$methods = [];
			$matchs = 0;
			//get the route args
			$argsRoute = explode("/", self::$routes[$j]['route']);
			//verify if the args quantity is equals the url args
			if (count($args) == count($argsRoute)) {
				//iterate the args
				$arguments = array();
				for ($i = 0; $i < count($args); $i++) {
					//if arg is not a variable
					if (strpos($argsRoute[$i], "@") === false) {
						//if the arg is in url
						if ($args[$i] == $argsRoute[$i]) {
							if (self::verifyMethod(self::$routes[$j]['method'])) {
								$controller = self::$routes[$j]['controller'];
								$method = self::$routes[$j]['method'];
								$methods = self::$routes[$j]['method'];
								$filter = self::$routes[$j]['filters'];
								$tokennized = self::$routes[$j]['tokennized'];
								$matchs++;
							}
						}
					} else {
						//if the arg is variable
						if (self::verifyMethod(self::$routes[$j]['method'])) {
							$controller = self::$routes[$j]['controller'];

							//add to args list to a function
							array_push($arguments, Request::sanitize($args[$i]));
							$method = self::$routes[$j]['method'];
							$methods = self::$routes[$j]['method'];
							$filter = self::$routes[$j]['filters'];
							$tokennized = self::$routes[$j]['tokennized'];
							$matchs++;
						}
					}
				}
				if ($matchs == count($args)) {
					$match = true;
					break;
				}
			}
		}

		if ($match) {
			if ($tokennized) {
				if (count(Request::post(['_token'])) > 0) {
					if (Request::post(['_token'])['_token'] != Request::session('internal_token')) {
						throw new \Exception("The route needs a auth Token.", 2);
					}
				} else {
					throw new \Exception("The route needs a auth Token.", 2);
				}
			}

			//verify if method is allowed
			self::verifyMethod($method);
			self::verifyFilters($filter);
			$allowed = Config::config('allowed-headers');
			foreach ($allowed as $header) {
				header($header);
			}
			$alloweMethods = '';
			foreach ($methods as $item) {
				$alloweMethods .= $item . ',';
			}
			header('Access-Control-Request-Method: ' . rtrim($alloweMethods, ','));

			if(Request::method() === 'OPTIONS'){
				exit;
			}
			//get the controller and function
			$controller = explode("@", $controller);
			if (count($controller) != 2) {
				throw new \Exception("The route needs 'nameClasse@method'.", 2);
			}
			(new Make)::make($controller[0], $controller[1], $arguments, (new self));
		} else {
			HTTP::statusHTTP(404);
		}
		return new static;
	}

	public static function Routes()
	{
		self::openRoute();
		return new static;
	}

	/**
	 * Verify the filters
	 */
	private static function verifyFilters(array $filters)
	{
		$filterClass = new Filter;
		foreach ($filters as $item => $value) {
			if (method_exists($filterClass, $value)) {
				$returned = $filterClass->$value();
				if ($returned !== true) {
					if (isset($filterClass->filters)) {
						if (isset($filterClass->filters[$value])) {
							if (isset($filterClass->filters[$value]['api'])) {
								if ($filterClass->filters[$value]['api'] === true) {
									if (is_array($returned)) {
										$http_status = (isset($returned['http_status']) ? $returned['http_status'] : 200);
										if (isset($returned['http_status'])) {
											unset($returned['http_status']);
										}
										response($returned, $http_status);
									} else {
										response($returned);
									}
									exit;
								}
							}
							if (isset($filterClass->filters[$value]['redirect'])) {
								header('location: ' . $filterClass->filters[$value]['redirect']);
								exit;
							}
						}
						HTTP::statusHTTP(403);
					}
				}
			} else {
				throw new \Exception("Filter '$value' not found");
			}
		}
	}
}
