<?php

	namespace App\Models;
	use \Web\Models\Request;
	use Web\Models\File;

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

		public function id() {
            return $this->id;
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

		static public function create( string $username, string $email, string $password, array | File $avatar = Null) {
			$credentials = ["username" => $username, "email" => $email, "password" => $password];
			$status = $GLOBALS["database"]->insert("users", $credentials);
			if ( !$status ) {
			}
			if ( $avatar <> Null ) {
				$name = $GLOBALS["database"]->last_id();
				if ($avatar->move(destination: "/public/avatars/", name: $name)) {
					$GLOBALS["database"]->update("users", ["avatar" => $name], ["email" => $email]);
				} else {
				}
			}
			return true;
		}

		static public function update( int $id, string $email, string $username, File $avatar = Null ) {
			$update = $GLOBALS["database"]->update("users", ["email" => $email, "username" => $username], ["id" => $id]);
			if ( $avatar <> Null ) {
				$name = $id;
				if ( File::exists("/public/avatars/{$name}.{$avatar->extension}")) {
					unlink("{$GLOBALS['rootPath']}/public/avatars/{$name}.{$avatar->extension}");
                }
				if ($avatar->move(destination: "/public/avatars/", name: $name)) {
					$update = $GLOBALS["database"]->update("users", ["avatar" => $name], ["id" => $id]);
				} else {
				}
            }
			//var_dump($update);
			//throw new \ErrorException("xd");
			if ( $update ) {
                return true;
            } else {
                return false;
            }

		}

		static public function all() {
			$details = $GLOBALS["database"]->fetch( [ "id", "username", "email", "is_owner", "avatar" ], "users");
			$users = array();
			foreach ( $details as $one ) {
				$users[] = new self($one["id"], $one["username"], $one["email"], $one["is_owner"], $one["avatar"]);
			}
			return $users;
		}

		static public function by_id($id) {
            $user = $GLOBALS["database"]->fetch( [ "id", "username", "email", "is_owner", "avatar" ], "users", [ "id" => $id ]);
			$user = $user[0];
			return new self($user["id"], $user["username"], $user["email"], $user["is_owner"], $user["avatar"]);
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