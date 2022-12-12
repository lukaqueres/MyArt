<?php
    namespace Web\Controllers;
    use Web\Models\Controller;
    use Web\Models\Redirect;
    use ErrorException;
    use Web\Models\View;

    include_once("{$GLOBALS["rootPath"]}/dependencies/User.php");
    include_once("{$GLOBALS["rootPath"]}/dependencies/Session.php");

    use Dependencies\Session;

    use Dependencies\User;

    class AuthController extends Controller {

        public static function authorize() {
            if ( isset($_SESSION["authorized"]) && $_SESSION["authorized"] === true ) {
                Redirect::to( url:"/myart", statusCode: 303, exit: true);
            }

            $user = User::authenticate(email: $_POST["email"], password: $_POST["password"]);
            # dump($user);
            if ( $user ) {
                $_SESSION["authorized"] = true;
                $_SESSION["user"] = $user;
				Redirect::to( url:"/myart", statusCode: 303, exit: true);
            } else {
                Session::flash("error", "Invalid email or password provided, please make corrections and try again.");
                Session::flash("email", $_POST["email"]);
                Redirect::to( url:"/myart?auth=login", statusCode: 303, exit: true);
            }
        }

        public static function unauthorize() {
            if ( isset($_SESSION["authorized"]) && $_SESSION["authorized"] === true ) {
                if ( !(session_status() === PHP_SESSION_NONE) ) {
				    session_destroy();
				}
            }
            Redirect::to( url:"/myart", statusCode: 303, exit: true);
        }
    }