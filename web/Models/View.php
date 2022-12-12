<?php

    namespace Web\Models;

    class View {
        public $name;

        static function return($name) {
            $path = str_replace(".", "/", $name);
            $GLOBALS["view"] = $path;

            require "public/page.php";
        }
    }