<?php
    namespace Web\Controllers;
    use Web\Models\Controller;

    use Web\Models\View;

    class MainController extends Controller {
        public static function index() {
            View::return("welcome");
        }
    }