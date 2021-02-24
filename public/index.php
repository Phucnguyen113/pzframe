<?php
    require '../app/core/app.php';
    require '../app/core/request.php';
    require_once '../app/core/database.php';
    require_once '../app/core/route.php';
    $DB=new DB;
    $DB->table('user')->select(['username','password'])->where('user_id','=','1')->where('username','LIKE',"%text%")->mapSql();
    new app;
    Route::get('/get1/{id}',function($id,Request $request){
        echo $request->get('a');
        echo $id;
        echo '<br><form method="post" action="http://localhost/php/pzframe/public/postEdit"><input type="submit" value="submit"></form>';
    })->name('name get 1');
    Route::get('/get2/{id}/{id2}','homeController@index');
    Route::post('/postEdit','homeController@postEdit');
    Route::group(['prefix'=>'pgroup/'],function(){
        Route::get('gr1','homeController@postEdit');
    });
    Route::mapingRoute(new Request);
   
?>