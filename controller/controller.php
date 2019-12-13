<?php
require_once 'config.php';
require_once './controller/login.php';

function route($query = null)
{
    global $view;

    $method = strtolower($_SERVER['REQUEST_METHOD']);
    $params = explode('/', $query);

    $controller = $params[0];
    $file_controller = './controller/' . $controller . '.php';

    if (file_exists($file_controller) && $controller !== 'controller'
        && $controller !== 'view' && $controller !== 'model') {

        require_once $file_controller;

        if ($controller === 'login') {
            $action = $controller;
        } else {
            if($method !== 'get') {
                if (isLogged()) {
                    switch ($method) {
                        case 'put':
                            $action = "upd_$controller";
                            break;
                        case 'delete':
                            $action = "del_$controller";
                            break;
                        case 'post':
                            $action = "add_$controller";
                            break;
                        default:
                            $action = null;
                    }
                } else {
                    return sendjson(['confirm' => false, 'info' => "Erro, sem permissao para acessar API!"]);
                }
            }else {
                $action = "get_$controller";
            }
        }

        if ($action && $method === 'get')
            if (sizeof($params) == 2) sendjson($action($params[1]));
            elseif (sizeof($params) == 3) sendjson($action($params[1], $params[2]));
            elseif (sizeof($params) == 4) sendjson($action($params[1], $params[2], $params[3]));
            else sendjson($action());
        elseif ($action)
            if ($method === 'put' || $method === 'delete') {
                sendjson($action(json_decode(file_get_contents("php://input"))));
            } else {
                sendjson($action());
            }
        else sendjson(['confirm' => false, 'info' =>'Erro de requisição','error'=>"ERROR ACTION REQUEST"]);

    } else {
        sendjson(['msg'=>'BAD REQUEST']);
    }
}

function sendjson($var = NULL)
{
    header('Content-Type: application/json');
    echo json_encode($var);
}


function debug($var)
{
    echo '<pre>';
    error_reporting(E_ALL);
    throw  new Exception("Debugando:\n########\nVAR:\n" .
        debug_vardump_backtrace($var) . '\n');
    echo '</pre>';
    die;
}

function debug_vardump_backtrace($var)
{
    ob_start();
    var_dump($var);
    debug_print_backtrace();
    $trace = ob_get_contents();
    ob_end_clean();
    return $trace;
}

function isAssoc(array $arr){
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}