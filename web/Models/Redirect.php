<?php

	namespace Web\Models;

	use Dependencies\Session;

	class Redirect {
		static public function to( $url, $statusCode = 303, $exit = true ) {
			header('Location: ' . $url, true, $statusCode );
			if ( $exit ) { die(); }
		}

		static public function back($error = Null, $message = Null) {
			if ( $error ) {
				Session::flash("error", $error);
			}

			if ( $message ) {
				Session::flash("message", $message);
			}
			header('Location: ' . $_SERVER['HTTP_REFERER'], true, 303);

			return die();
		}
	}		