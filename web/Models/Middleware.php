<?php
namespace Web\Models;

    use Dependencies\Session;
    use Web\Models\Redirect;

    class Middleware {

        static public function authUser() {
            if ( Session::has("user") && Session::get("authorized") ) {
                return true;
            } else {
                Redirect::back(error: "Authentication failed");
                return false;
            }
        }

        static public function authOwner() {
            if ( Session::has("user") && Session::get("authorized") && Session::get("user")->is_owner === true) {
                return true;
            } else {
                Session::set("error", "You can not do that");
                Redirect::back(error: "Authentication failed");
                return false;
            }
        }
    }