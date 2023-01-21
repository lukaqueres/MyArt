<?php

	namespace Web\Controllers;

	use Dependencies\Session;
	use App\Models\User;

	use Web\Models\Request;
	use Web\Models\View;
	use Web\Models\Redirect;
	use Web\Models\Controller;

	class MyArtController extends Controller {

		static public function overview() {
			View::return("myart.overview");
		}

		static public function users() {
			View::return("myart.users");
		}

		static public function articles() {
			View::return("myart.articles");
		}
	}