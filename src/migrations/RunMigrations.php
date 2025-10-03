<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

use core\Database;

try {
    $pdoConnect = Database::getInstance();

    // Caminho para o arquivo SQL
    $sqlFile = __DIR__ . '/sql_migrations.sql';

    // Lê todo o conteúdo do arquivo
    $sql = file_get_contents($sqlFile);

    if ($sql === false) {
        throw new Exception("Erro ao ler o arquivo SQL");
    }

    // Divide por ponto e vírgula (;) para separar as queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));

    // Executa cada query
    foreach ($queries as $query) {
        if (!empty($query)) {
            $pdoConnect->exec($query);
        }
    }

    echo "Migrations executadas com sucesso!";

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
