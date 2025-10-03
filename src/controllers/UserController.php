<?php

namespace controllers;

use models\User;
use jwt\JWT;

//require_once __DIR__ . '/../src/jwt/JWT.php';

class UserController {

    public $usuario;
    
    public function __construct(){
        $this->usuario = new User();
    }

    public function userById() {        
        $id = $_GET['id'] ?? null;  

        if ($id === null) {
            echo json_encode(['error' => 'ID não informado Here?']);
            return;
        }        
        $result = $this->usuario->getById($id);

        echo json_encode($result);
    }

    public function userName() {
        $nome = $_GET['nome'] ?? null; //PARA METODO GET

        if ($nome === null) {
            echo json_encode(['error' => 'ID não informado ??']);
            return;
        }
        $resp = $this->usuario->getByName($nome);
        echo json_encode($resp);
    }

    public function UsuarioPorDepartamento() {        
        $codigoDepartamento = $_GET['id_departamento'] ?? null;
        
        if(!empty($codigoDepartamento)){
            $resp = $this->usuario->getByUsuarioPorDepartamento($codigoDepartamento);
            if($resp == null){
                echo json_encode(['mensagem' => 'Nenhum usuário encontrado para o departamento informado']);
            }else{
                echo json_encode($resp);
            }
        }else{
            echo json_encode(['mensagem' => 'Parâmetro id_departamento não informado']);
        }
    }

    public function usersList() {
        $resp = $this->usuario->getAll();
        echo json_encode($resp);
    }
    
    public function authenticate() {
        /* $email = $_POST['email'] ?? null;
        $senha = $_POST['senha'] ?? null; */

        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';

        $user = $this->usuario->findByEmail($email);
        
        if ($email === null || $senha === null) {
            echo json_encode(['error' => 'Dados não informado']);
            return;
        }
        
        $resp = $this->usuario->authenticate($email, $senha);
        /* $ls = [ 'user' => $resp,
                'nome' => "Jackson Neves",
                'email' => "jackson@example.com",
                'idade' => 33,
            ]; */
        //echo json_encode($ls);

        if ($resp) {
            $user = $this->usuario->findByEmail($email);
    
            $secretKey = "minha_chave_super_secreta";
    
            $payload = [
                "sub" => $user['id'],
                "email" => $user['email'],
                //"nivel" => $user['nivel_permissao'],
                "iat" => time(),
                "exp" => time() + 3600 // expira em 1 hora
            ];
    
            $jwt = JWT::createJWT($payload, $secretKey);
    
            echo json_encode([
                "authenticated" => true,
                "token" => $jwt
            ]);
        } else {
            echo json_encode([
                "authenticated" => false,
                "message" => "Usuário ou senha inválidos"
            ]);
        }

        //echo json_encode($resp);
    }


}