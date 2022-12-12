<!DOCTYPE html>
<html lang="en">
	<head>
		<?php require "elements/head.php"; ?>
	</head>
	<body>
        <?php
		if ( isset($_SESSION["authorized"]) && $_SESSION["authorized"] === true ) {
			require_once "public/elements/Panel.php";
		} elseif ( isset($_GET["auth"]) && $_GET["auth"] === "login") {
			include_once( "public/elements/loginSidePanel.php");
		}


		require "views/{$GLOBALS["view"]}.php";

		if ( isset($_SESSION["authorized"]) && $_SESSION["authorized"] === true ) { ?>
			<script src="./public/resources/js/panel.js"></script>
		<?php } ?>
        <script src="./public/resources/js/user.js"></script>
		<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
		<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
	</body>
</html>