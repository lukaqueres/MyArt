<?php
namespace Dependencies;
use \ErrorException;

    class Helpers {

        static public function redirect( $url, $statusCode = 303, $exit = true ) {
            header('Location: ' . $url, true, $statusCode );
            if ( $exit ) { die(); }
        }

        static public function dump($text) {
            throw new ErrorException($text);
        }

        static public function fstring(string $string, array $values) { // TODO: Finish string formatting function
            $string_array = preg_split("/[{,}]+/", $string);
            $final = array();
            foreach ( $string_array as $part ) {
				if ( $part ) {}
			}
			foreach ( $values as $name => $value ) {
				$string = str_replace("\{$name\}", $value, $string);
			}
		}

    }