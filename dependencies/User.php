<?php

	namespace Dependencies;

	use Database\Databse;

	class User {
		protected int $id;
		public string $username;
		public string $email;
		public string $avatar;
		public bool $is_owner;

		function __construct( $id, $username, $email, $is_owner, $avatar) {
			$this->id = $id;
			$this->username = $username;
			$this->email = $email;
			$this->avatar = $this->avatar($avatar);
			$this->is_owner = $is_owner;
		}

		static public function authenticate(string $email, string $password) {
			$data = $GLOBALS["database"]->fetch( [ "id", "username", "email", "password", "is_owner", "avatar" ], "users", [ "email"=> $email ], true);
			if ( !$data ) {
				return false;
			}
			# dump(var_dump($data));

			$verify = password_verify($password, $data["password"]);
			if ( !$verify ) {
				return false;
			}

			$user = new self($data["id"], $data["username"], $data["email"], $data["is_owner"], $data["avatar"]);
			return $user;

		}

		static public function create( string $username, string $email, string $password, array $avatar = Null) {
			$credentials = ["username" => $username, "email" => $email, "password" => $password];
			$status = $GLOBALS["database"]->insert("users", $credentials);
			//var_dump($status);
			//throw new \ErrorException("STATUS");
			if ( !$status ) {
				//return $status;
			}
			if ( $avatar <> Null ) {
				$name = $GLOBALS["database"]->last_id();
				$credentials["avatar"] = $avatar;
				$extension  = pathinfo( $avatar["name"], PATHINFO_EXTENSION );
				if (move_uploaded_file($avatar["tmp_name"], "{$GLOBALS['rootPath']}/public/avatars/{$name}.{$extension}")) {
					$GLOBALS["database"]->update("users", ["avatar" => $name], ["email" => $email]);
					//echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
				} else {
					//echo "Sorry, there was an error uploading your file.";
				}
				//var_dump($avatar);
				//throw new \ErrorException("Avatar not null");

			}
			return true;
		}

		static public function all() {
			$details = $GLOBALS["database"]->fetch( [ "id", "username", "email", "is_owner", "avatar" ], "users");
			$users = array();
			foreach ( $details as $one ) {
				$users[] = new self($one["id"], $one["username"], $one["email"], $one["is_owner"], $one["avatar"]);
			}
			return $users;
		}

		static public function authorized() {
			if ( isset($_SESSION["user"]) ) {
				return true;
			} else {
				return false;
			}
		}

		public function avatar( string $avatar = Null) {
			$dbAvatar = $GLOBALS["database"]->fetch( [ "avatar" ], "users", [ "email"=> $this->email ], true);
			if ( $dbAvatar ) {
				$avatar = $avatar?$avatar:$dbAvatar["avatar"];
			}
            $avatar = "public/avatars/" . $avatar . ".png";
			if (!file_exists($GLOBALS["rootPath"] . "\\" . $avatar) || !str_ends_with($avatar, ".png")) {
                $avatar = "public/avatars/default_pic.png";
            }

			$this->avatar = $avatar;
			return $this->avatar;
        }

		public function refresh() {
            $data = $GLOBALS["database"]->fetch( [ "id", "username", "email", "password", "is_owner", "avatar" ], "users", [ "email"=> $this->email ], true);
			if ( !$data ) { return false; }
			#var_dump($data);
			#throw new \ErrorException("");
			$this->id = $data["id"];
			$this->username = $data["username"];
			$this->email = $data["email"];
			$this->is_owner = $data["is_owner"];
			$this->avatar($data["avatar"]);
			return true;
        }
	}