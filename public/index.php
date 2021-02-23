<?php
    require '../app/core/app.php';
    require '../app/core/request.php';
    require_once '../app/core/route.php';
    new app;
    Route::get('/gét1','controller1@action1');
    Route::get('/gét2','controller2@action1');
    Route::get('/gét3','controller3@action1');
    Route::group(['prefix'=>'pgroup/'],function(){
        Route::get('gr1','gr1nha');
    });
    Route::mapingRoute();
?>