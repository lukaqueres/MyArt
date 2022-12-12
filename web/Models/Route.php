<?php

    namespace Web\Models;

    class Route {
        public $url;
        public $callable;
        public $variables;

        static public function get( $url, $callable ) {
            $path = $_SERVER['REQUEST_URI'];
            $path = strtok($path,'?');
            if ( $url == $path ) {
                if ( !in_array($_SERVER["REQUEST_METHOD"], ["GET", "get"] )) {
                    throw new \ErrorException("Request method invalid, use GET only");
                }
                $callable();
                return true;
            } else {
                return false;
            }
        }

        static public function post( $url, $callable) {
            $path = $_SERVER['REQUEST_URI'];
            $path = strtok($path,'?');
            if ( $url == $path ) {
                if ( !in_array($_SERVER["REQUEST_METHOD"], ["POST", "post"] )) {
                    throw new \ErrorException("Only POST method suppoerted for this url");
                }
                $callable();
                return true;
            } else {
                return false;
            }
        }

    }