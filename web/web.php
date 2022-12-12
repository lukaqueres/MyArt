<?php

namespace  Web;

    use Web\Models\Route;
    use Web\Models\View;
    use Web\Models\Redirect;
    use Web\Models\Middleware;

    use Web\Controllers\MainController;
    use Web\Controllers\MyArtController;
    use Web\Controllers\AuthController;

    //var_dump($GLOBALS["request"]);
    //throw new \ErrorException($_SERVER['REQUEST_URI']);

    if ( Route::get("/myart/", function () {
        MainController::index();
    })) { return; };
    if ( Route::get("/myart/login", function () {
        View::return("login");
    })) { Middleware::authUser(); return; };

    if ( Route::post("/myart/authorize", function () {
        AuthController::authorize();
    })) { return; };

    if ( Route::get("/myart/unauthorize", function () {
        AuthController::unauthorize();
    })) { return; };

    if ( Route::get("/myart/myart", function () {
        MyArtController::overview();
    })) { Middleware::authUser(); return; };

    if ( Route::post("/myart/users/transfer_owner", function () {
        MyArtController::changeOwner();
    })) { Middleware::authOwner(); return; };

    if ( Route::post("/myart/users/edit/user", function () {
        MyArtController::editUser();
    })) { Middleware::authOwner(); return; };

    if ( Route::get("/myart/articles", function () {
        MyArtController::articles();
    })) { Middleware::authUser(); return; };

    # var_dump($_SERVER['REQUEST_URI']);

    Redirect::to($_SERVER['REQUEST_URI'], statusCode: 404, exit: true);