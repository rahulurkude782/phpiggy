<?php

declare(strict_types=1);

namespace Framework;

use PDO, PDOException;
use PDOStatement;



class Database
{
    private PDO $connection;
    private PDOStatement $stmt;
    public function __construct(string $driver, array $config, string $username, string $password)
    {
        $config = http_build_query(data: $config, arg_separator: ';');
        $dsn = "{$driver}:{$config}";
        try {
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die('Unable to connect to database.');
        }
    }

    public function query(string $sqlQuery, array $params = []): Database
    {
        $this->stmt = $this->connection->prepare($sqlQuery);
        $this->stmt->execute($params);
        return $this;
    }

    public function id()
    {
        return $this->connection->lastInsertId();
    }

    public function count(): mixed
    {
        return $this->stmt->fetchColumn();
    }

    public function get()
    {
        return $this->stmt->fetch();
    }

    public function all()
    {
        return $this->stmt->fetchAll();
    }
}
