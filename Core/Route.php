<?php


class Route
{
    static function start(){
        $controller_name = 'Main';
        $action_name = 'index';

        $routes = explode('/', $_SERVER['REQUEST_URI']);

        if(!empty($routes[1])){
            $controller_name = $routes[1];
        }

        if(!empty($routes[2])){
            $action_name = $routes[2];
        }

        if(!empty($routes[3])){
            $param = $routes[3];
        }

        $model_name = 'Model_'.$controller_name;
        $controller_name = 'Controller_'.$controller_name;
        $action_name = 'action_'.$action_name;

        $model_file = strtolower($model_name).'.php';
        $model_path = 'Model/'.$model_file;

        if(file_exists($model_path)){
            include 'Model/'.$model_file;
        }

        $controller_file = strtolower($controller_name).'.php';
        $controller_path = 'Controller/'.$controller_file;

        if(file_exists($controller_path)){
            include 'Controller/'.$controller_file;
            $controller = new $controller_name;
            $action = $action_name;

            if(method_exists($controller, $action)){
                if(isset($param)){
                    $controller->$action($param);
                }else{
                    $controller->$action();
                }

            }else{
                Route::ErrorPage404();
            }
        }else{
            Route::ErrorPage404();
        }


    }

   public static function ErrorPage404(){
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        //echo $host;
        header('HTTP/1.1 404 Not Found');
       header('Status: 404 Not Found');
        header('Location:'.$host.'404');
    }
}