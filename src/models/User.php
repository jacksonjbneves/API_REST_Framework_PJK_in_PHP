<?php
namespace models;

use core\Database;
use PDO;

class User
{
    private PDO $ConnectDB;

    public function __construct(){
        // Obtém a conexão PDO do Database singleton
        $this->ConnectDB = Database::getInstance();
    }

    // =======================
    // Métodos CRUD básicos
    // =======================

    // Buscar usuário por ID
    public function getById(int $id): array|false {
        $stmt = $this->ConnectDB->prepare("SELECT id, nome, email, nivel_permissao FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    //Buscar por nome
    public function getByName(string $nome): ?array {
        $stmt = $this->ConnectDB->prepare("SELECT id, nome, email, nivel_permissao FROM usuarios WHERE nome = :nome");
        $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
        $stmt->execute();        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }

    public function getByUsuarioPorDepartamento($codigo_departamento): ?array{
        $sql = "SELECT u.id, u.nome, u.email FROM usuarios u
                    JOIN departamento d ON u.id_departamento = d.codigo_departamento
                WHERE d.codigo_departamento = :codigo_departamento";

        $stmt = $this->ConnectDB->prepare($sql);        
        $stmt->bindValue(":codigo_departamento", $codigo_departamento, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $users ?: null;
        /* $table = [];
        return $table ?: null; */
    }

    // Buscar usuário por email - com boas praticas e seguras
    public function findByEmail(string $email): ?array {
        $stmt = $this->ConnectDB->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
        
    // Buscar todos os usuários
    public function getAll(): array {
        $stmt = $this->ConnectDB->prepare("SELECT id, nome, email, nivel_permissao FROM usuarios");        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Criar novo usuário
    public function create(array $data): bool {
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
    }

    // Atualizar usuário
    public function update(int $id, array $data): bool {
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
    }

    // Deletar usuário
    public function delete(int $id): bool {
        $stmt = $this->ConnectDB->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Autenticação simples
    public function authenticate(string $email, string $senha): bool {
        $stmt = $this->ConnectDB->prepare("SELECT senha FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            return true;
        }

        return false;
    }

}
