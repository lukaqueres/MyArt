<?php

namespace  Web;

	use Web\Models\Route;
	use Web\Models\View;
	use Web\Models\Redirect;
	use Web\Models\Middleware;

	/*
	use Web\Controllers\UserController;
	use Web\Controllers\MainController;
	use Web\Controllers\MyArtController;
	use Web\Controllers\AuthController;
	*/
	//var_dump($GLOBALS["request"]);
	//throw new \ErrorException($_SERVER['REQUEST_URI']);

	$request = $GLOBALS["request"];


	Route::get("/myart/", "MainController@index");

	Route::post("/myart/authorize", "AuthController@authorize");

	Route::get("/myart/unauthorize", "AuthController@unauthorize");

	Route::get("/myart/myart",
		"MyArtController@overview",
		middleware: Middleware::authUser());

	Route::get("/myart/articles",
		"MyArtController@articles",
		middleware: Middleware::authUser());

	/*
	 * User management
	*/

	Route::post("/myart/users/me/edit",   // - Edit user from current session -
		"UserController@editMe",
		middleware: Middleware::authUser()); 
	Route::post("/myart/users/{id}/edit", // - Edit selected user -
		"UserController@edit",
		middleware: Middleware::authOwner()); 

	Route::post("/myart/users/{id}/change-owner", // - Change owner to selected user -
		"UserController@changeOwner",
		middleware: Middleware::authOwner()); 

	Route::post("/myart/users/add",       // - Create new user -
		"UserController@add",
		middleware: Middleware::authOwner());

	Route::post("/myart/users/{id}/delete",
		"UserController@delete",
		middleware: Middleware::authOwner()); // - Delete selected user -

	# var_dump($_SERVER['REQUEST_URI']);

	Redirect::to($_SERVER['REQUEST_URI'], statusCode: 404, exit: true);