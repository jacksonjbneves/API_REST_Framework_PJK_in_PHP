<?php
namespace core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    // Construtor privado para impedir instanciação externa
    private function __construct() {}

    // Método para obter a conexão PDO
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // lança exceções em erros
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // fetch padrão como array associativo
                    PDO::ATTR_EMULATE_PREPARES => false, // usa prepared statements reais
                ]);
            } catch (PDOException $e) {
                die("Erro de conexão: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
