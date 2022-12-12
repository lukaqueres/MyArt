<?php
	namespace Web\Models;

	class Validate {
		static public function form($settings) {
			foreach($settings as $value => $methods ) {
				foreach ( $methods as $m ) {
					switch ($m) {
						case 'required':
							# code...
							break;
	
						default:
							# code...
							break;
					}
				}
			}
		}

		protected function required($value) {
			return ( isset($value) && $value <> "");
		}
	}