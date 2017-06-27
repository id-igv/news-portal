<?php
    /**
     * Author: Ilia Goloub
     *         i.goloub@gmail.com
     * 
     * Main entrence of this app
     * 
    */
    
    error_reporting(E_ALL);
    
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', __DIR__ . DS . '..' . DS);
    define('VIEW', ROOT . 'View' .  DS);
    define('ROUTES', ROOT . 'Config' .  DS . 'routes.php');
    define('DB_PARAMS', ROOT . 'Config' .  DS . 'db_connection.php');
    define('IMGS', DS . 'img' . DS);
    
    spl_autoload_register(function ($className) {
        $file_to_require = ROOT . str_replace('\\', DS, "{$className}.php");
        
        if (!file_exists($file_to_require)) {
            throw new \Exception('<br>File <b>' . $file_to_require . '</b> not found. Error log:<br> class name -> ' . $className . ';<br> file -> ' . __FILE__ . ';<br> line -> ' . __LINE__ . '<hr>');
        }
        
        require_once($file_to_require);
    });
    
    \Library\Session::start();
    
    $container = new \Library\Container();
    $request = new \Library\Request();
    
    $router = new \Library\Router(ROUTES, $request);
    
    // fills in $container to be able get access to global app params
    $container->set('router', $router);
    $container->set('request', $request);
    
    //------------DB-CONNECTION
    try {
        extract( require(DB_PARAMS));
        
        $conn = new \PDO("mysql:host=$servername;dbname=$databasename", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $container->set('pdo_connection', $conn);
        
    } catch(\PDOException $e) {
        Session::setFlash("<br>Connection with DB failed: " . $e->getMessage() . '<br>Try again later');
        
    } catch(\Exception $e) {
        Session::setFlash('<br>' . $e->getMessage() . '<br>');
    }
    //-------------------------
    
    $route = $router->getCurrentRoute();
    $controller = 'Controller\\' . $route->get_controller() . 'Controller';
    $controller = new $controller();
    $controller->set_container($container);
    $action = $route->get_action() . 'Action';
    echo $controller->$action();
    
?>