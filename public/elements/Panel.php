<?php
	use Dependencies\User;
	use Dependencies\Session;

	use Dependencies\HTMLObject;
?>
<link rel="stylesheet" href="./public/resources/css/panel.css">

<manage-panel id="panel" class="right">
	<div id="panel-bar">
		<panel-button class="ion-icon ionic-clear" data-button_category="articles" ><ion-icon name="albums-outline"></ion-icon></panel-button>
		<panel-button class="ion-icon ionic-clear" data-button_category="pages" ><ion-icon name="document-outline"></ion-icon></panel-button>
		<panel-button class="ion-icon ionic-clear" data-button_category="users" ><ion-icon name="people-outline"></ion-icon></panel-button>
		<panel-button class="ion-icon ionic-clear" data-button_category="settings" ><ion-icon name="settings-outline"></ion-icon></panel-button>
		<panel-button href="./unauthorize" class="log-out ion-icon ionic-clear"><ion-icon name="exit-outline" ></ion-icon></panel-button>
	</div>
	<div id="panel-main">
		<div class="panel-category" data-element_category="articles">
			Articles
		</div>
		<div class="panel-category" data-element_category="pages">
			Pages
		</div>
		<div class="panel-category" data-element_category="users">
			<?php
			$myDiv = new HTMLObject(tag: "user-details");
			$myDiv->attributes->add(["avatar" => Session::get("user")->avatar(), "username"  => Session::get("user")->username, "email" => Session::get("user")->email, "edit" => true]);
			print $myDiv;
			?>
			<p>Users:</p>
			<div class="user-list">
				<?php
				foreach( User::all() as $user ) {
					$userDiv = new HTMLObject(tag: "user-details");
					$userDiv->attributes->add(["avatar" => $user->avatar, "username"  => $user->username, "email" => $user->email]);
					if ( Session::get("user")->is_owner && Session::get("user")->email <> $user->email ) {
						$userDiv->attributes->add(["edit" => true, "transfer_owner" => true]);
					}
					print $userDiv;
				}
				?>
                <user-details typeof="add_new_user"></user-details>
			</div>
				
		</div>
		<div class="panel-category" data-element_category="settings">
			Settings
		</div>
        <div class="panel-notification-container">
            <?php
			$error = Session::getFlash("error");
			$message = Session::getFlash("message");
			if ( $error ) {
				print new HTMLObject(tag: "div", text: $error, classList: ["panel-notification-error"]);
            }
			if ( $message ) {
				print new HTMLObject(tag: "div", text: $message, classList: ["panel-notification-message"]);
			} 
			?>
        </div>
	</div>
</manage-panel>