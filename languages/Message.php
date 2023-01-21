<?php
namespace languages;

	class Message {

		static public array $validate_messages = [
			"no_required_value" => "Value %s is required",
			"value_no_type" => "Value %s must be a %s",
			"value_no_email" => "%s must be a correct email",
			"value_not_uniqe" => "%s incorrect value: %s is already used, please change and try again",
			"length_too_short" => "Length of %s must be greater than %d",
			"length_not_in_range" => "Length of %s must fit between specified length", // TODO: Change this string to better one
			"number_not_in_range" => "Value of %s must fit between specified length", // TODO: Change this string to better one
			"length_too_long" => "Value of %s is too long, reduce to maximum %d",
			"value_not_image" => "%s must be an image",
			"value_extension_not_supported" => "%s with %s extension is not supported",
			"file_size_too_large" => "%s file size too large",
			"not_a_file" => "%s must be a file ",
			"value_not_exist" => "%s with %s does not exist"
		];

		public static function validate_response($message, $name, $value = Null): string {
			$string = self::$validate_messages[$message];
			$string = sprintf($string, $name, $value);
			return $string;
		}
	}