<?php

	namespace Web\Controllers;

	use Dependencies\Session;
	use App\Models\User;

	use Web\Models\Request;
	use Web\Models\View;
	use Web\Models\Redirect;
	use Web\Models\Controller;

	class UserController extends Controller {

		static public function edit($request) {
			$id = $GLOBALS["request"]->get("id")->value;
			$email = $request->validate::scheme(
				scheme: ["email" => ["unique:users"],
				],
				request: $request,
				redirectInvalid: false,
				);

			//var_dump($id);
			//var_dump(User::by_id($id));
			//print $request->post("email")->value . "    " . User::by_id($id)->email;
			//throw new \Error();

			if ( !$email ) {
				if ( $request->post("email")->value <> User::by_id($id)->email ) {
					return Redirect::back(error: "Email is already taken");
				}
			}

			$credentials = $GLOBALS["request"]->validate::scheme(
				scheme: [
					"username" => ["required", "string", "between:4,30"],
					"email" => ["required", "email", "between:5,50"],
					"avatar" => ["file", "image", "extension:png", "size:10000,1000000"]
				],
				request: $GLOBALS["request"]);
			if ( $credentials ) {
				$credentials = $GLOBALS["request"]->validate::scheme(
					scheme: ["id" => ["required", "exists:users"]],
					request: $GLOBALS["request"],
					force_method: "GET",
					redirectInvalid: false
				);
			}
			if ( !$credentials ) { return Redirect::back(error: "Invalid request"); }
			if ( $request->file("avatar")) {
				$update = User::update(id: $id, email: $request->post("email")->value, username: $request->post("username")->value,avatar: $request->file("avatar"));
			} else {
				$update = User::update(id: $id, email: $request->post("email")->value, username: $request->post("username")->value);
			}

			if ( $update ) {
				Redirect::back(message: "Record updated");
			} else {
				Redirect::back(error: "Error while updating");
			}
		}

		static public function editMe($request) {
			$email = $request->validate::scheme(
				scheme: ["email" => ["unique:users"],
				],
				request: $request,
				redirectInvalid: false,
				);

			if ( !$email ) {
				if ( $request->post("email")->value <> Session::user()->email ) {
					return Redirect::back(error: "Email is already taken");
				}
			}


			$credentials = $GLOBALS["request"]->validate::scheme(
			scheme: [
				"username" => ["required", "string", "between:4,30"],
				"email" => ["required", "email", "between:5,50"],
				"avatar" => ["file", "image", "extension:png", "size:10000,1000000"]
			],
			request: $request);

			$user_id = Session::user()->id();

			if ( !$credentials ) { return Redirect::back(error: "Invalid request"); }

			if ( $request->file("avatar")) {
				$update = User::update(id: $user_id, email: $request->post("email")->value, username: $request->post("username")->value, avatar: $request->file("avatar"));
			} else {
				$update = User::update(id: $user_id, email: $request->post("email")->value, username: $request->post("username")->value);
			}
			if ( $update ) {
				Redirect::back(message: "User details updated");
			} else {
				Redirect::back(error: "Error while updating");
			}
		}

		static public function changeOwner($request) {
			$valid = $request->validate::scheme(
				scheme: [
					"id" => ["required", "exists:users"],
					],
					force_method: "GET",
					request: $request,
					redirectInvalid: false);
			if ( !$valid ) { return Redirect::back(error: "Invalid request"); }
			$change = $GLOBALS["database"]->exec("UPDATE users SET is_owner = 0"); // TODO - Change used
			$change = $GLOBALS["database"]->exec("UPDATE users SET is_owner = 1  WHERE id = {$request->get('id')}"); // TODO - Change used function
			if ( $change ) {
				Redirect::back(message: "Owner updated");
			} else {
				Redirect::back(error: "Error while updating");
			}
		}

		static public function add($request) {
			$request->validate::scheme(
				scheme: [
				"username" => ["required", "string", "between:4,30"],
				"email" => ["required", "email", "unique:users", "between:5,50"],
				"password" => ["required", "string", "between:8,30"],
				"avatar" => ["file", "image", "extension:png", "size:10000,1000000"]
				],
				request: $request);

			$password = password_hash($request->post("password"), PASSWORD_DEFAULT);

			if ( !$request->file("avatar")->empty ) {
				$insert = User::create($request->post("username"), $request->post("email"), $password, $request->file("avatar") );
			} else {
				$insert = User::create($request->post("username"), $request->post("email"), $password);
			}
			if ( $insert ) {
				Redirect::back(message: "User saved");
			} else {
				Redirect::back(error: "Error while saving");
			}
		}

		static public function delete($request) {
			$request->validate::scheme(
				scheme: [
					"id" => ["required", "exists:users"],
					],
					force_method: "GET",
					request: $request);
			$affected = $GLOBALS["database"]->delete("users", ["id" => $request->get("id")->value ]);
			if ( $affected > 0 ) {
				Redirect::back(message: "User deleted");
			} else {
				Redirect::back(error: "Error while deleting");
			}
		}
	}