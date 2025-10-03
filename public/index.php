<?php
declare(strict_types=1);
//ACRIAÇÃO API

//Ambiente Local
require_once __DIR__ . '/../src/config/config.php';

//echo phpinfo();

// Autoload simples (sem Composer)
spl_autoload_register(function($class) {    
    $baseDir = dirname(__DIR__) . '/src/';

    $classPath = str_replace('\\', '/', $class);
    $file = $baseDir . $classPath . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use core\Router;

// Iniciar o roteador
$router = new Router();

// Rotas da API (retornam JSON)

//Authenticador
$router->post('/api/authenticate', 'controllers\\UserController@authenticate');

// Usuarios
$router->get('/api/userid', 'controllers\\UserController@userById');
$router->get('/api/username', 'controllers\\UserController@userName');
$router->get('/api/usuario/usuario-departamento', 'controllers\\UserController@UsuarioPorDepartamento'); //Exemplo de uso:
$router->get('/api/userslist', 'controllers\\UserController@usersList');

// Chamadas
$router->get('/api/chamada/id-chamada/{id}', 'controllers\\ChamadaController@IDChamada');
$router->patch('/api/chamada/{id}/lida', 'controllers\\ChamadaController@ChamadaLida');
$router->post('/api/chamada/lista-chamadas', 'controllers\\ChamadaController@ListaChamadas');
$router->post('/api/chamada/adicionar-chamada', 'controllers\\ChamadaController@AdicionarChamada');

$router->post('/api/chamada/filtra/lista-chamadas', 'controllers\\ChamadaController@FiltraChamadas');


//Anexos
$router->get('/api/chamada/download-anexo/(.*)', 'controllers\\ChamadaController@DownloadAnexo');


header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

?>
