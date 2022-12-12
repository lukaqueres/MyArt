<?php
namespace Web\Models;

    use Dependencies\Session;

    class Middleware {

        static public function authUser() {
            if ( Session::has("user") && Session::get("authorized") ) {
                return true;
            } else {
                redirect( url:$GLOBALS["auth_redirect_unauthorized"], statusCode: 303, exit: true);
                return false;
            }
        }

        static public function authOwner() {
            if ( Session::has("user") && Session::get("authorized") && Session::get("user")->is_owner === true) {
                return true;
            } else {
                Session::set("error", "You can not do that");
                redirect( url:$GLOBALS['urlPath'], statusCode: 303, exit: true);
                return false;
            }
        }
    }