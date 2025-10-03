<?php
namespace models;

use core\Database;
use PDO;
use PDOException;

class Chamada{

    private PDO $ConnectDB;

    public function __construct(){
        // Obtém a conexão PDO do Database singleton
        $this->ConnectDB = Database::getInstance();
    }

    // Buscar usuário por ID
    /* public function getById(int $id): array|false {
        $stmt = $this->ConnectDB->prepare("SELECT id, nome, email, nivel_permissao FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    } */

    public function getByIDChamada($id) : array | false {
        $stmt = $this->ConnectDB->prepare("SELECT * FROM chamadas WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function postByIDChamadaLida(int $id): bool {
        $sql = "UPDATE chamadas SET chamada_notificada = FALSE WHERE id = :id";    
        $stmt = $this->ConnectDB->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }

    //Buscar por nome
    /* public function getByName(string $nome): ?array {
        $stmt = $this->ConnectDB->prepare("SELECT id, nome, email, nivel_permissao FROM usuarios WHERE nome = :nome");
        $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
        $stmt->execute();        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    } */

    // Buscar usuário por email - com boas praticas e seguras
    /* public function findByEmail(string $email): ?array {
        $stmt = $this->ConnectDB->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    } */

    public function EscolherColaboradorReceberChamada(){
        //Logica de verificar por departamento, e ver quem  tem menos chamadas abertas
        // enviar a chamada para o que tem menos
    }

    //Cria Chamada
    public function AdicionarChamada(array $data) : bool {
        //$pdo = Database::getInstance();

        // Gerar código da chamada (ex: CH-2025-7856)
        //$codigo = "CH-" . date("Y") . "-" . rand(1000, 9999);

        $sql = "INSERT INTO chamadas 
                (codigo_chamada, departamento, user_id_destination, user_id, status, titulo, mensagem, anexos, data_abertura)
                VALUES 
                (:codigo_chamada, :departamento,:user_id_destination, :user_id, :status, :titulo, :mensagem, :anexos, :data_abertura)";

        $stmt = $this->ConnectDB->prepare($sql);

        return $stmt->execute([
            ":codigo_chamada" => $data['codigo_chamada'],
            ":departamento"   => $data['departamento'],
            ":user_id_destination" => $data['user_id_destination'],
            //Ajustar. deve colocar o metodo => EscolherColaboradorReceberChamada()
            //":user_id_destination" => rand(1,5), 
            ":user_id"        => $data['user_id'], 
            ":status"         => $data['status'] ?? 'ABERTO',
            ":titulo"         => $data['titulo'],
            ":mensagem"       => $data['mensagem'],
            ":anexos"         => $data['anexos'] ?? null,
            //":anexos" => isset($data['anexos']) ? json_encode($data['anexos']) : null,
            ":data_abertura"  => date("Y-m-d H:i:s"),
        ]);    
    }
        
    // Buscar todas as Chamadas de um usuário
    public function getAll($userID): array {        
        $sql = "SELECT c.id, c.codigo_chamada, d.nome_departamento AS departamento, c.user_id_destination,
                       c.user_id, c.status, c.titulo, c.mensagem, c.anexos,
                       DATE_FORMAT(c.data_abertura, '%d-%m-%Y %H:%i:%s') AS data_abertura,
                       DATE_FORMAT(c.data_fechamento, '%d-%m-%Y %H:%i:%s') AS data_fechamento,
                       c.chamada_notificada 
                FROM chamadas c
                     INNER JOIN departamento d ON c.departamento = d.codigo_departamento
                WHERE c.user_id_destination = :user_id_destination ORDER BY c.data_abertura DESC";
        
        $params[':user_id_destination'] = $userID;

        $stmt = $this->ConnectDB->prepare($sql);
        $stmt->execute($params);
        $table = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $table;
    }

    //Filtra Chamadas
    public function getByFiltraChamadas(array $filtro) : array | false{
        
        try {
            $sql = 'SELECT c.id, c.codigo_chamada, d.nome_departamento AS departamento, c.user_id_destination, c.user_id, 
                           c.status, c.titulo, c.mensagem, c.anexos, 
                           DATE_FORMAT(c.data_abertura, "%d-%m-%Y %H:%i:%s") AS data_abertura, 
                           DATE_FORMAT(c.data_fechamento, "%d-%m-%Y %H:%i:%s") AS data_fechamento, 
                           c.chamada_notificada
                    FROM chamadas c
                           INNER JOIN departamento d ON c.departamento = d.codigo_departamento
                    WHERE' ;
            $params = [];            
    
            if(!empty($filtro['chamadaRecEnv'])){
               $chamadaRecEnv = $filtro['chamadaRecEnv'];
               //Chamadas recebidas
               //FROM chamadas WHERE user_id_destination = :user_id_destination";
               if($chamadaRecEnv == 1){
                  $sql .= " c.user_id_destination = :user_id_destination";
                  $params[':user_id_destination'] = $filtro['user_id'];
               }
               //Chamadas enviadas
               //FROM chamadas WHERE user_id = :user_id";               
               if($chamadaRecEnv == 2){
                  $sql .= " c.user_id = :user_id";
                  $params[':user_id'] = $filtro['user_id'];
               }
            }else{
                return [
                    "error" => true,
                    "message" => "Erro em Chanadas Recebidas e Enviadas"
                ];
            }
            
            // status
            !empty($filtro['status']) ? ($sql .= " AND c.status LIKE :status") && ($params[':status'] = "%" . $filtro['status'] . "%") : null;
            // departamento
            !empty($filtro['departamento']) ? ($sql .= " AND c.departamento = :departamento") && ($params[':departamento'] = $filtro['departamento']) : null;            
            // search
            !empty($filtro['search']) ? ($sql .= " AND c.titulo LIKE :titulo") && ($params[':titulo'] = "%" . $filtro['search'] . "%") : null;    
            
            $sql .= " ORDER BY c.data_abertura DESC";
    
            $stmt = $this->ConnectDB->prepare($sql);
            $stmt->execute($params);
            $table = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            //return ["table"=>$table, "sql" => $sql];
            //return ["sql" => $sql];
            return $table;
    
        } catch (PDOException $e) {
            // Retorna um array vazio, mas você poderia retornar erro detalhado também
            return [
                "error" => true,
                "message" => "aqui > ".$e->getMessage()
            ];
        }
    }
    // Criar novo usuário
    /* public function create(array $data): bool {
        $stmt = $this->ConnectDB->prepare("
            INSERT INTO usuarios (nome, senha, nivel_permissao, email)
            VALUES (:nome, :senha, :nivel_permissao, :email)
        ");

        // Hash da senha antes de salvar
        $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);

        return $stmt->execute([
            'nome' => $data['nome'],
            'senha' => $data['senha'],
            'nivel_permissao' => $data['nivel_permissao'],
            'email' => $data['email'],
        ]);
    } */

    // Atualizar usuário
    /* public function update(int $id, array $data): bool {
        $stmt = $this->ConnectDB->prepare("
            UPDATE usuarios
            SET nome = :nome,
                email = :email,
                nivel_permissao = :nivel_permissao
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'nome' => $data['nome'],
            'email' => $data['email'],
            'nivel_permissao' => $data['nivel_permissao'],
        ]);
    } */

    // Deletar usuário
    /* public function delete(int $id): bool {
        $stmt = $this->ConnectDB->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    } */

    // Autenticação simples
    /* public function authenticate(string $email, string $senha): bool {
        $stmt = $this->ConnectDB->prepare("SELECT senha FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            return true;
        }

        return false;
    } */

}
