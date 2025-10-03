<?php

namespace controllers;

use models\Chamada;
use Exception; 
use jwt\JWT;

//require_once __DIR__ . '/../src/jwt/JWT.php';

class ChamadaController {

    public $chamada;
    
    public function __construct(){
        $this->chamada = new Chamada();
    }

    /* public function userById() {        
        $id = $_GET['id'] ?? null;  

        if ($id === null) {
            echo json_encode(['error' => 'ID não informado Here?']);
            return;
        }        
        $result = $this->chamada->getById($id);

        echo json_encode($result);
    } */

    /* public function userName() {
        $nome = $_GET['nome'] ?? null;

        if ($nome === null) {
            echo json_encode(['error' => 'ID não informado ??']);
            return;
        }
        $resp = $this->chamada->getByName($nome);
        echo json_encode($resp);
    } */

    public function IDChamada( $id = null){
        if ($id === null) {
            echo json_encode(['error' => 'ID da chamada não informado']);
            return;
        }
        $resp = $this->chamada->getByIDChamada((int) $id); //pode ter erro de string, converter para int
        echo json_encode($resp);
    }

    public function ChamadaLida($id = null){
        if ($id === null) {
            echo json_encode(['error' => 'ID da chamada não informado']);
            return;
        }
    
        $resp = $this->chamada->postByIDChamadaLida((int)$id);
    
        if ($resp) {
            echo json_encode(['success' => true, 'message' => "Chamada $id marcada como lida"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Erro ao atualizar chamada $id"]);
        }
    }

    public function ColaboradorReceberChamada(){



    }

    public function AdicionarChamada(){        

        $nomesArquivos = [];
        $codigoChamada = "CH-" . date("Y") . "-" . rand(1000,9999);        
        if (isset($_FILES['anexos'])) {
            foreach ($_FILES['anexos']['tmp_name'] as $key => $tmpName) {
                $nomeOriginal = $_FILES['anexos']['name'][$key];
                $novoNome = $codigoChamada."_file_".rand(1000,9999)."_".$nomeOriginal;                
                $CaminhoArquivo = dirname(__DIR__, 2) . '/anexos/corpservice/' .$novoNome;                
                if (!move_uploaded_file($tmpName, $CaminhoArquivo)) {
                    http_response_code(500);
                    header('Content-Type: application/json');
                    echo json_encode([
                        "erro" => "Não foi possível salvar o arquivo: $nomeOriginal"
                    ]);
                    exit;//para a execução
                }
                $nomesArquivos[] = $novoNome;
            }
        }
    
        //Transforma o array em JSON para salvar no banco
        $input['anexos'] = json_encode($nomesArquivos);
    
        // Demais campos devem vir do corpo da requisição (POST/JSON)
        
        $input['codigo_chamada'] = $codigoChamada;
        $input['departamento'] = $_POST['departamento'] ?? null;
        $input['user_id_destination'] = $_POST['user_id_destination'] ?? null;
        $input['user_id'] = $_POST['user_id'] ?? null;
        $input['titulo'] = $_POST['titulo'] ?? null;
        $input['mensagem'] = $_POST['mensagem'] ?? null;
    
        $ok = $this->chamada->AdicionarChamada($input);
    
        header('Content-Type: application/json'); // garante JSON
        if ($ok) {
            http_response_code(201);            
            echo json_encode(["sucesso" => "Chamada criada com sucesso",
                              "CodigoChamada" => $codigoChamada]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Falha ao criar chamada"]);
        }
    }

    public function ListaChamadas() {
        try {

           $datas = json_decode(file_get_contents("php://input"), true);              
           //$resp = $datas['user_id'];

           $resp = $this->chamada->getAll($datas['userID']);
           //adicionar ajuste de aparacer apenas a chamada que pertence ao usuario
           echo json_encode($resp);

        } catch (Exception $e) {
            echo json_encode([
                "error" => true,
                "message" => $e->getMessage()
            ]);
        }
    }
    
    public function FiltraChamadas() {
        try {
            $datas = json_decode(file_get_contents("php://input"), true);

            $status = [1 => 'ABERTO', 2=>'EM ANDAMENTO', 3 => 'FECHADO', 4 => 'CANCELADO'];
            $filtro = [
                'user_id'        => $datas['user_id'] ?? null,
                'chamadaRecEnv'  => $datas['chamadasRecEnv'] ?? null, //verificar as que foram enviadas e recebidas
                'status'         => isset($datas['status'], $status[$datas['status']]) ? $status[$datas['status']] : null,
                'departamento'   => $datas['departamento'] ?? null,
                'search'         => $datas['search'] ?? null,
            ];
            
            $resp = $this->chamada->getByFiltraChamadas($filtro);            
            /* echo json_encode(["ListaChamadaFiltro" => $resp["table"],
                              "SQL"  => $resp["sql"]
                             ]); */
            /* echo json_encode(["SQL"  => $resp["sql"]]); */
            echo json_encode($resp);
    
        } catch (Exception $e) {
            echo json_encode([
                "error" => true,
                "message" => $e->getMessage()
            ]);
        }
    }

    /* public function authenticate() {

        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';

        $user = $this->chamada->findByEmail($email);
        
        if ($email === null || $senha === null) {
            echo json_encode(['error' => 'Dados não informado']);
            return;
        }
        
        $resp = $this->chamada->authenticate($email, $senha);
        

        if ($resp) {
            $user = $this->chamada->findByEmail($email);
    
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
    } */

    public function DownloadAnexo($filename) {
        $filePath = dirname(__DIR__, 2) . "/anexos/corpservice/" . basename($filename);
    
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo json_encode(["error" => "Arquivo não encontrado"]);
            return;
        }
    
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . basename($filePath));
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
        exit;
    }


}