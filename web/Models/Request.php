<?php

namespace Web\Models;

#use Web\Models\Redirect;
use Languages\Message;

	class InvalidRequestMethod extends \ErrorException {};

	class Request {
		public string $method;
		public string $adress;
		public array $values = array();
		public array $files = array();
		protected array $post_values = array();
		protected array $get_values = array();
		public string $validate = Validate::class;

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
				$empty = false;
				foreach ( $file as $name => $properity ) {
					if ( !$properity && $name <> "error" ) {
						$empty = true;
						break;
					}
				}
				if ( $empty ) {
					$this->files[] = new File($key, $file, empty: true);
				} else {
					$this->files[] = new File($key, $file);
				}
			}
		}

		public function get($key) {
			$element = array_filter(
				$this->get_values,
				function ($o) use ($key) {
					return $o->name == $key;
				}
			);
			if ( count( $element ) < 1 ) {
				return false;
			}
			return reset($element);
		}

		public function post($key) {
			$element = array_filter(
				$this->post_values,
				function ($o) use ($key) {
					return $o->name == $key;
				}
			);
			if ( count( $element ) < 1 ) {
				return false;
			}
			return reset($element);
		}

		public function file($key) {
			$file = array_filter(
				$this->files,
				function ($o) use ($key) {
					return $o->key == $key;
				}
			);
			if ( count( $file ) < 1 ) {
				return false;
			}
			return reset($file);
		}

		public function addVar( $key, $value, $method = "POST" ) {
			$variable = new Variable($key, $value);
			$this->values[] = $variable;
			if ( $method == "POST" ) {
				$this->post_values[] = $variable;
			} elseif ( $method == "GET" ) {
				$this->get_values[] = $variable;
			}
		}
	}

	class Variable {
		public string $name;
		public mixed $value;

		public function __construct(string $name, mixed $value) {
			$this->name = $name;
			$this->value = trim($value);
		}

		public function __toString(): string {
			return $this->value;
		}
	}

	class Validate {

		public static function scheme($scheme, Request $request, string | bool $force_method = "POST", bool $redirectInvalid = true): bool {
			$resultSet = array();
			foreach ( $scheme as $name => $settings ) {
				if ( in_array("file", $settings) ) {
					$variable = $request->file($name);
					if ( $variable === false || $variable->empty ) {
						$variable = false;
						$value = false;
					}
					var_dump($variable);
					$value = $variable->name;
				} else {
					switch ( $force_method ) {
						case "POST":
							$variable = $request->post($name);
							break;

						case "GET":
							$variable = $request->get($name);
							break;

						case false:
							$variable = $request->post($name) || $value = $request->get($name);
							break;

						default:
							throw new \BadMethodCallException("Method {$force_method} is not suported, use POST, GET, or false for no method specified.");
					}
					if ( !is_bool($variable)) {
						$value = $variable->value;
					}
				}

				if ( in_array("required", $settings) && ( $value === false || $variable === false)) {
					if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("no_required_value", $name, $value)); }
					else { return false; }
				} elseif ( $value === false || $variable === false) {
					$resultSet[$name] = $value;
					break;
				}
				foreach ( $settings as $check ) {
					if ( str_contains($check, ":")) {
						switch (strtok($check, ':')) {
							case 'exists':
								$exp = explode(':', $check, 2);
								$table = end($exp);
								if ( $GLOBALS["database"]->exists($table, [$name => $value]) ) {
									if ($check == end($settings)) {
										$resultSet[$name] = $value;
									}
								} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("value_not_exist", $name, $value)); }
									else { return false; }

								}
								break;

							case 'unique':
								$exp = explode(':', $check, 2);
								$table = end($exp);
								if ( $GLOBALS["database"]->exists($table, [$name => $value]) ) {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("value_not_uniqe", $name, $value)); }
									else { return false; }
								} else {
									if ($check == end($settings)) {
										$resultSet[$name] = $value;
									}
								}
								break;

							case 'between':
								$exp = explode(',', $check, 2);
								$max = end($exp);
								$exp = explode(':', explode(',', $check, 2)[0], 2);
								$min = end($exp);
								//throw new \ErrorException("xd");
								if ( is_string($value)) {
									if ( $max >= strlen($value) && strlen($value) >= $min ) {
										if ($check == end($settings)) {
											$resultSet[$name] = $value;
										}
									} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("length_not_in_range", $name, $value)); }
									else { return false; }
									}
								} elseif ( is_int($value)) {
										if ( $min >= $value && $value >= $max ) {
										if ($check == end($settings)) {
											$resultSet[$name] = $value;
										}
									} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("number_not_in_range", $name, $value)); }
									else { return false; }
									}
								}
								break;

							case 'extension':
								if ( property_exists($variable, "tmp_name")) {
									$extension = strtolower(pathinfo(basename($variable->name),PATHINFO_EXTENSION));
									$exp =explode(':', $check, 2);
									if ($extension == end($exp)) {
										if ($check == end($settings)) {
											$resultSet[$name] = $value;
										}
									} else {
										if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("value_extension_not_supported", $name, $extension)); }
										else { return false; }
									}
								} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("not_a_file", $name, $value)); }
									else { return false; }
								}
								break;

							case 'same':
								$exp = explode(':', $check, 2);
								$var = end($exp);
								if ( $force_method == "POST" ) {
									$var = $request->post($var);
								} elseif ( $force_method == "GET") {
									$var = $request->get($var);
								}
								if ( $var === false ) {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("value_not_uniqe", $name, $value)); }
									else { return false; }
								}
								if ( $value == ($var->value) ) {
									if ($check == end($settings)) {
										$resultSet[$name] = $value;
									}
								} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("value_not_uniqe", $name, $value)); }
									else { return false; }
								}
								break;

							case 'size':

								if ( property_exists($variable, "tmp_name")) {
									$exp = explode(',', $check, 2);
									$max = end($exp);
									$exp = explode(':', explode(',', $check, 2)[0], 2);
									$min = end($exp);
									if ( $max >= $variable->size && $variable->size >= $min ) {
										if ($check == end($settings)) {
											$resultSet[$name] = $value;
										}
									} else {
										if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("file_size_too_large", $name, $value)); }
										else { return false; }
									}
								} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("not_a_file", $name, $value)); }
									else { return false; }
								}
								break;
						}
					} else {
						switch ($check) {
							case 'required':
								if ( self::required($value) ) {
									if ($check == end($settings)) {
										$resultSet[$name] = $value;
									}
								} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("no_required_value", $name, $value)); }
									else { return false; }
								}
								break;

							case 'string':
								if ( self::is_string($value) ) {
									if ($check == end($settings)) {
										$resultSet[$name] = $value;
									}
								} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("value_no_type", $name, $value)); }
									else { return false; }
								}
								break;

							case 'email':
								if ( self::is_email($value) ) {
									if ($check == end($settings)) {
										$resultSet[$name] = $value;
									}
								} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("value_no_email", $name, $value)); }
									else { return false; }
								}
								break;
							case 'image':
								if ( property_exists($variable, "tmp_name")) {
									var_dump($variable);
									$check = getimagesize($variable->tmp_name);
									if($check !== false) {
										if ($check == end($settings)) {
											$resultSet[$name] = $value;
										}
									} else {
										if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("value_not_image", $name, $value)); }
										else { return false; }
									}
								} else {
									if ( $redirectInvalid ) { Redirect::back(error: Message::validate_response("value_not_image", $name, $value)); }
									else { return false; }
								}
								break;
							default:
								# code...
								break;
						}
					}
				}
			}
			return true;
		}

		protected static function required($variable) {
			if ( is_string($variable) ) {
				$variable = preg_replace('/\s+/', '', $variable);
			}
			if ( $variable ) {
				return true;
			} else {
				return false;
			}
		}

		protected static function is_string($variable) {
			return is_string($variable);
		}

		protected static function is_email($variable) {
			return filter_var($variable, FILTER_VALIDATE_EMAIL);
		}



	}