<?php

namespace Dependencies;

use App\Models\User;

    class Session {
        protected array $reservedKeys = array("FLASH");

        function __construct() {
            session_start();
        }

        public static function destroy() {
            session_destroy();
        }

        public static function set($key, $value) {
            if ( in_array( $key, self::reservedKeys) ) { throw new Exception(' Key name reserved ');}
            if ( !is_string($key) ) { throw new Exception('String key required'); }
            $_SESSION[$key] = $value;
            return $value;
        }

        public static function get($key) {
            if ( !is_string($key) ) { throw new Exception('String key required'); }
            $value = $_SESSION[$key];
            return $value;
        }

        public static function clear(): void {
            session_unset();
        }

        public static function has(string $key): bool {
            return array_key_exists($key, $_SESSION);
        }

        public static function flash(string $key, $value) {
            if ( !isset($_SESSION["FLASH"]) ) { $_SESSION["FLASH"] = array(); }
            $_SESSION["FLASH"][$key] = $value;
        }

        public static function getFlash(string $key) {
            if ( !isset($_SESSION["FLASH"])) { return false; }
            if ( !array_key_exists($key, $_SESSION["FLASH"]) ) { return false; }
            $value = $_SESSION["FLASH"][$key];
            unset( $_SESSION["FLASH"][$key] );
            return $value;
        }

        public static function clearFlash() {
            if ( isset($_SESSION["FLASH"]) ) { unset( $_SESSION["FLASH"] ); }
        }

        public static function user() {
            if ( isset($_SESSION["user"]) ) { return $_SESSION["user"]; } else { return false; }
        }
    }