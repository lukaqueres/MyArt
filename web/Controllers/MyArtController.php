<?php

	namespace Web\Controllers;

	use Dependencies\Session;
	use Dependencies\User;

	use Web\Models\Request;
	use Web\Models\View;
	use Web\Models\Redirect;
	use Web\Models\Controller;

	class MyArtController extends Controller {

		static public function overview() {
			View::return("myart.overview");
		}

		static public function changeOwner() {
			if ( Session::has("user") && Session::get("user")->is_owner ) {
				$update = $GLOBALS["database"]->exec("UPDATE users SET is_owner = 0"); // TODO - Change used method
				$change = $GLOBALS["database"]->exec("UPDATE users SET is_owner = 1 "); // TODO - Change used method
			} else {
				Redirect::back(error: "You can not do this");
			}

			if ( $update ) {
				Redirect::back(message: "Record updated");
			} else {
				Redirect::back(error: "Error while updating");
			}
		}

		static public function editUser() {
			#var_dump($_POST);
			#throw new \ErrorException("xd");

			if ( !isset($_POST["email"]) || !isset($_POST["username"]) || !isset($_POST["action"])) {
				Session::flash("error", "Arguments not passed");
				return Redirect::back();
			}
			if ( !Session::has("user")) {
				Session::flash("error", "User not authenticated");
				return Redirect::back();
			}
			switch ( $_POST["action"] ) {
				case "edit_user":
					if (!isset($_POST["initialemail"])) {
						Redirect::back(error: "Missing required argument");
					}

					if (!$GLOBALS["database"]->exists("users", ["email" => $_POST["initialemail"]])) {
						Redirect::back(error: "Email {$_POST["initialemail"]} doesn't exist, please refresh page and try again.");
					}
					if ($GLOBALS["database"]->exists("users", ["email" => $_POST["email"]]) && $_POST["email"] <> $_POST["initialemail"] ) {
						Redirect::back(error: "Email {$_POST["initialemail"]} is already used by another user.");
					}

					if ( Session::get("user")->email == $_POST["initialemail"]) {
						$update = $GLOBALS["database"]->exec("UPDATE users SET email = '{$_POST["email"]}', username =  '{$_POST["username"]}' WHERE email = '{$_POST["initialemail"]}'"); // TODO - Change used method
						Session::user()->username = $_POST["username"];
						Session::user()->email = $_POST["email"];
					} else if ( Session::get("user")->is_owner ) {
						$update = $GLOBALS["database"]->exec("UPDATE users SET email = '{$_POST["email"]}', username =  '{$_POST["username"]}' WHERE email = '{$_POST["initialemail"]}'"); // TODO - Change used method
					} else {
						Session::flash("error", "You can not do this");
						return Redirect::back();
					}

					if ( $update ) {
						Redirect::back(message: "Record updated");
					} else {
						Redirect::back(error: "Error while updating");
					}
					break;

				case "change_owner":
					if ( !Session::get("user")->is_owner ) {
						Redirect::back(error: "You don't have enough permissions for this");
					}
					$change = $GLOBALS["database"]->exec("UPDATE users SET is_owner = 0"); // TODO - Change used
					$change = $GLOBALS["database"]->exec("UPDATE users SET is_owner = 1  WHERE email = '{$_POST["initialemail"]}'"); // TODO - Change used
					Session::user()->is_owner = false;
					if ( $change ) {
						Redirect::back(message: "Owner updated");
					} else {
						Redirect::back(error: "Error while updating");
					}
					break;

				case "add_user":

					/*
					Request::validate([
						"username" => ["required", "string"],
						"email" => ["required", "email", "Users:unique"],
						"password" => ["required", "string", "length:between[8,16]"],
						"avatar" => ["image", "extension:png", "size:max_500000"]
						]);
					*/

					if ( !isset($_POST["password"])) {
						Redirect::back(error: "No password provided");
					}

					if ( !isset($_POST["password"])) {
						Redirect::back(error: "No password provided");
					}
					$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
					//var_dump($_FILES);
					//throw new \ErrorException(!empty($_FILES["avatar"]["name"]));
					if ( !empty($_FILES["avatar"]["name"]) ) {
						$avatars_dir = "/myart/public/avatars/";
						$uploadOk = 1;

						$avatar = $_FILES["avatar"];
						$avatar_name = basename($avatar["name"]);

						$target_avatar = $avatars_dir . $avatar_name;

						$imageFileType = strtolower(pathinfo($target_avatar,PATHINFO_EXTENSION));

						$check = getimagesize($avatar["tmp_name"]);
						if ($check !== false) {
							#Redirect::back(error: "File is an image - " . $check["mime"] . ".");
							$uploadOk = 1;
						} else {
							Redirect::back(error: "File is not an image.");
							$uploadOk = 0;
						}


						// Check if file already exists
						if (file_exists($target_avatar)) {
						  Redirect::back(error: "Sorry, file already exists.");
						  $uploadOk = 0;
						}

						// Check file size
						if ($avatar["size"] > 1000000) {
						  Redirect::back(error: "Sorry, your file is too large.");
						  $uploadOk = 0;
						}

						// Allow certain file formats
						if ($imageFileType != "png") {
						  Redirect::back(error: "Sorry, only PNG.");
						  $uploadOk = 0;
						}

						$insert = User::create($_POST["username"], $_POST["email"], $password, $avatar );
					} else {
						$insert = User::create($_POST["username"], $_POST["email"], $password);
					}
					if ( $insert ) {
						Redirect::back(message: "User saved");
					} else {
						Redirect::back(error: "Error while saving");
					}
					break;
				default :
					Redirect::back(message: "Error while proceeding request");
					break;
			}
			Session::user()->refresh();
			Redirect::back();
		}

		static public function users() {
			View::return("myart.users");
		}

		static public function articles() {
			View::return("myart.articles");
		}
	}