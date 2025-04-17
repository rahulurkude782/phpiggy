<?php

declare(strict_types=1);

include __DIR__ . '/src/Framework/Database.php';

use Framework\Database;

$db = new Database('mysql', [
    'host' => 'localhost',
    'port' => 3306,
    'dbname' => 'phpiggy',
    'charset' => 'utf8mb4'
], 'root', '');

try {
    $sqlFile = file_get_contents('./database.sql');
    $db->query($sqlFile);
    // $result = $db->query(
    //     "SELECT transactions.*
    //     FROM transactions 
    //     LEFT JOIN receipts ON receipts.transaction_id = transactions.id
    //     WHERE transactions.user_id = 1"
    // )

    //     ->all();
    // var_dump($result);
} catch (PDOException $error) {
    echo $error->getMessage();
}
