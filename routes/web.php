<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});


Route::get('/status', function () {
    $dbstatus = "";
    $http_code = 200;
    $arr_options = [];
    if(getenv('MYSQL_SSL_CA')) {
        $arr_options = [ PDO::MYSQL_ATTR_SSL_CA => getenv('MYSQL_SSL_CA') ];
    }
    try {
        $database_handle = new PDO(
            "mysql:host=".getenv('DB_HOST').";dbname=".getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            $arr_options
        );
        $dbstatus = "connected";
    } catch (PDOException $e) {
        $dbstatus = "disconnected";
        $http_code = 503;
    }
    header($_SERVER["SERVER_PROTOCOL"] . " " . $http_code, true, $http_code);
    header('Content-type: application/json');
    echo json_encode([
        "healthy" => ($http_code == 200 && $dbstatus == "connected" ? true : false),
        "message" => ($http_code == 200 && $dbstatus == "connected" ? "success" : "error")
    ]);
});
