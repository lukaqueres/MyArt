<?php

namespace Web\Models;

use Web\Models\Redirect;
use Languages\Message;

	class InvalidRequestMethod extends \ErrorException {};

	class Request {
		public string $method;
		public string $adress;
		public array $values = array();
		public array $files = array();
		protected array $post_values = array();
		protected array $get_values = array();
		public Validate $validate;

		public function __construct() {
			$this->method = $_SERVER['REQUEST_METHOD'];
			$this->adress = strtok($_SERVER['REQUEST_URI'],'?');

			switch ( $this->method ) {
				case "POST":
					break;

				case "GET":
					break;

				default:
					throw new InvalidRequestMethod("Method `{$this->method}` is not supported");
			}

			if ( count($_POST) > 0 ) { $this->with_post(); }
			if ( count($_GET) > 0 ) { $this->with_get(); }

			if ( count($_FILES) > 0 ) { $this->with_files(); }

		}

		protected function with_get() {
			foreach ( $_GET as $key => $value) {
				$variable = new Variable($key, $value);
				$this->values[] = $variable;
				$this->get_values[] = $variable;
			}
		}

		protected function with_post() {
			foreach ( $_POST as $key => $value) {
				$variable = new Variable($key, $value);
				$this->values[] = $variable;
				$this->post_values[] = $variable;
			}
		}

		protected function with_files() {
			foreach ( $_FILES as $key => $file ) {
				$this->files[] = new File($key, $file);
			}
		}

		public function get($key) {
			$element = array_filter(
				$this->get_values,
				function ($o) use ($key) {
					return $o->name != $key;
				}
			);
			if ( count( $element ) < 1 ) {
				return false;
			}
			return $element[0];
		}

		public function post($key) {
			$element = array_filter(
				$this->post_values,
				function ($o) use ($key) {
					return $o->name != $key;
				}
			);
			if ( count( $element ) < 1 ) {
				return false;
			}
			return $element[0];
		}

		public function file($key) {
			$file = array_filter(
				$this->files,
				function ($o) use ($key) {
					return $o->key != $key;
				}
			);
			if ( count( $file ) < 1 ) {
				return false;
			}
			return $file[0];
		}

		///////////////////////////////////////////////////////
		/*
		public static function validate( $validation ) {
			$passes = array();
			foreach ( $validation as $name => $checks ) {
				if ( empty($_POST[$name]) && in_array("required", $checks)) {
					return Redirect::back(error: "Missing required argument {$name}");
				} elseif (empty($_POST[$name])) {
					continue;
				} else {
					$value = $_POST[$name];
					foreach ( $checks as $check ) {
						switch ($check) {
							case "string":
								if ( !self::validate_string($value) ) {
									return Redirect::back(error: "Value {$name} must be a string");
								}
								break;
							case "email":
								if ( !self::validate_email($value) ) {
									return Redirect::back(error: "Value of {$name} field must be a correct email");
								}
								break;
							case "Users:unique":
								if ( !self::validate_users_unique($name, $value) ) {
									return Redirect::back(error: "Provided value in field {$name} is already taken");
								}
								break;
							case "file":
								if ( !self::validate_file($name) ) {
									return Redirect::back(error:  "{$name} must be an image");
								}
								break;
							default:
								# code...
								break;
						}
					}
				}
			}
		}

		protected static function validate_string($value) {
			return is_string($value);
		}

		protected static function validate_email($value) {
			return filter_var($value, FILTER_VALIDATE_EMAIL);
		}

		protected static function validate_users_unique($field, $value) {
			return !$GLOBALS["database"]->exists("users", [$field => $value]);
		}

		protected static function validate_file($name) {
			if ( empty($_FILES["fileToUpload"][$name])) {
				return false;
			}
			$check = getimagesize($_FILES["fileToUpload"][$name]);
			if ( $check !== false ) {
				return true;
			} else {
				return false;
			}
		}
	*/
	}

	class Variable {
		public string $name;
		public mixed $value;

		public function __construct(string $name, mixed $value) {
			$this->name = $name;
			$this->value = $value;
		}
	}

	class File {
		public string $key;
		public string $name;
		public string $type;
		public string $extension;
		public string $tmp_name;
		public int $error;
		public int $size;

		public function __construct($key, $file) {
			$this->key = $key;
			$this->name = $file["name"];
			$this->type = $file["type"];
			$this->extension = pathinfo( $file["name"], PATHINFO_EXTENSION );
			$this->tmp_name = $file["tmp_name"];
			$this->error = $file["error"];
			$this->size = $file["size"];
		}

		public function move($destination, $root = true, $name = Null ): bool {
			$name = $name?$name:$this->name;
			$path = $root?$GLOBALS['rootPath']:"";
			if ( !str_ends_with($destination, "/") ) { $destination .= "/"; };
			if ( !str_starts_with($destination, "/") ) { $destination = "/" . $destination; };
			$path .= "{$destination}{$name}.{$this->extension}";
			$status = move_uploaded_file($this->tmp_name, $path);
			return $status;
		}
	}

	class Validate {
		public static function scheme($scheme, Request $request, string | bool $force_method = "POST") {
			foreach ( $scheme as $name => $settings ) {
				if ( in_array("file", $settings) ) {
					$value = $request->file($name);
				} else {
					switch ( $force_method ) {
						case "POST":
							$value = $request->post($name);
							break;

						case "GET":
							$value = $request->get($name);
							break;

						case false:
							$value = $request->post($name) || $value = $request->get($name);
							break;

						default:
							throw new \BadMethodCallException("Method {$force_method} is not suported, use POST, GET, or false for no method specified.");
					}
				}
				if ( in_array("required", $settings) && $value === false ) {
					Redirect::back(error: Message::validate_response("no_required_value", $name, $value));
				} elseif ( $value === false ) {
					break;
				}


			}
		}
	}