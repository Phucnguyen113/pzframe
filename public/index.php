<?php
    require '../app/core/app.php';
    require '../app/core/request.php';
    require_once '../app/core/middleware.php';
    require_once '../app/core/database.php';
    require_once '../app/core/route.php';
    $request = new Request;
    new app;
    // $db=new DB;
    // $name="Nguyen Trong Phuc";
    // $db->table('user')->select(['Name','Email'])->skip(0)->take(10)->where([
    //     ['name','=','Nguyen Trong Phuc']
    // ])->orWhere([
    //     ['pass','LIKE','adminphuc']
    // ])->mapSql();
    Route::get('/',function(){
        echo 123;
    });
    Route::get('get1/{id}',function($id, Request $req){
        echo $req->get('a');
        echo $id;
        echo '<br><form method="post" action="http://localhost/php/pzframe/public/postEdit"><input type="submit" value="submit"></form>';
    })->name('name get 1');
    Route::get('/get2/{id}/{id2}','homeController@index')->middleware('auth');
    Route::post('/postEdit','homeController@postEdit');
    Route::group(['prefix'=>'pgroup/'],function(){
        Route::get('gr1','homeController@postEdit');
    });
    Route::mapingRoute(new Request);
   
?>