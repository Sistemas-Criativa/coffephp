<?php

use Core\Route;

//define the routes, and add a prefix to a Controller folder
Route::prefixController("Controllers", function (Route $Route) {
        //Add the routes (routes names is optional)
        $Route->name('home')->Get("/","\IndexController@show");
        $Route->name('show.login')->Get("/login","\LoginController@show");
        $Route->name('do.login')->Post("/login","\LoginController@login");
        $Route->name('do.logout')->Get("/logout","\LoginController@logout");
        $Route->name('show.recover')->Get("/recover","\RecoverController@show");
        $Route->name('do.recover')->Post("/recover","\RecoverController@recover");
        $Route->name('show.reset')->Get("/reset/@token","\RecoverController@showReset");
        $Route->name('do.reset')->Post("/reset/@token","\RecoverController@reset");
        $Route->name('show.signup')->Get("/signup","\SignController@show");
        $Route->name('do.signup')->Post("/signup","\SignController@sign");
        $Route->name('dashboard')->Filter(['auth'])->Get("/dashboard","\IndexController@show");

        //route to api call
        $Route->Match("/api/login", "\LoginController@loginAPI", ["POST", "HEAD", "OPTIONS"], false);

        //protected route from api call
        $Route->Filter(['authByAPI'])->Match("/api/dashboard", "\IndexController@showAPI", ["GET", "HEAD", "OPTIONS"], false);
});

