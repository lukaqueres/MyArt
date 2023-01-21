<?php

	namespace Web\Models;

	use Web\Models\Middleware;

	//use Web\Controllers\MainController;

	class Route {
		public $url;
		public $callable;
		public $variables;

		static public function get( $url, $callable, $middleware = Null ) {
			$request = $GLOBALS["request"];
			$path = $_SERVER['REQUEST_URI'];
			$path = strtok($path,'?');

			$arrurl = explode("/", $url);
			//var_dump($arrurl);
			$arrpath = explode("/", $path);
			//var_dump($arrpath)

			if ( count($arrurl) <> count($arrpath)) {
				return false;
			}
			for($i = 0; $i < count($arrurl); $i++ ) {
				$parturl = $arrurl[$i];
				$partpath = $arrpath[$i];
				//var_dump($partpath);
				//var_dump($parturl);
				if ( $partpath == $parturl ) {
					continue;
				} elseif ( preg_match("/^{.*}$/", $parturl) ) {
					$varName = str_replace(['{', '}'], '', $parturl);
					$varValue = $partpath;
					$GLOBALS["request"]->addVar($varName, $varValue, method: "GET");
					continue;
				} else {
					return false;
				}
			}
			//throw new \ErrorException("checking method");
			if ( !in_array($_SERVER["REQUEST_METHOD"], ["GET", "get"] )) {
				throw new \ErrorException("Request method invalid, use GET only");
			}

			$callable = explode("@", $callable, 2);
			$callable[0] = "Web\Controllers\\" . $callable[0];

			if ( $middleware <> Null ) {
                if ( $middleware ) {
					call_user_func($callable, $request);
                }
				die();
            } else {
                call_user_func($callable, $request);
				die();
            }
			//return true;

		}

		static public function post( $url, $callable, $middleware = Null ) {
			$request = $GLOBALS["request"];
			$path = $_SERVER['REQUEST_URI'];
			$path = strtok($path,'?');

			$arrurl = explode("/", $url);
			//var_dump($arrurl);
			$arrpath = explode("/", $path);
			//var_dump($arrpath);

			if ( count($arrurl) <> count($arrpath)) {
				//throw new \ErrorException("Request method invalid, use GET only");
				return false;
			}
			for($i = 0; $i < count($arrurl); $i++ ) {
				$parturl = $arrurl[$i];
				$partpath = $arrpath[$i];
				//var_dump($partpath);
				//var_dump($parturl);
				if ( $partpath == $parturl ) {
					continue;
				} elseif ( preg_match("/^{.*}$/", $parturl) ) {
					$varName = str_replace(['{', '}'], '', $parturl);
					$varValue = $partpath;
					$GLOBALS["request"]->addVar($varName, $varValue, method: "GET");
					continue;
				} else {
					return false;
				}
			}
			if ( !in_array($_SERVER["REQUEST_METHOD"], ["POST", "post"] )) {
				throw new \ErrorException("Request method invalid, use POST only");
			}
			$callable = explode("@", $callable, 2);
			$callable[0] = "Web\Controllers\\" . $callable[0];
			if ( $middleware <> Null ) {
                if ( $middleware ) {
					call_user_func($callable, $request);
                }
				die();
            } else {
                call_user_func($callable, $request);
				die();
            }
			//return true;
		}

	}