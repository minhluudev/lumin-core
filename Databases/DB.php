<?php

namespace Lumin\Databases;

use Lumin\Databases\Interfaces\DBInterface;
use Lumin\Databases\Traits\MigrationTrait;
use Lumin\Helper;
use Lumin\Support\Facades\Log;
use PDO;
use PDOException;


class DB implements DBInterface {
    use MigrationTrait;

    protected PDO $pdo;

    public function applyMigrations(): void {
        if (!isset($this->pdo)) {
            $this->connectToDatabase();
        }
        $this->handleApplyMigrations($this->pdo);
    }

    public function connectToDatabase(): void {
        $config     = Helper::config('db');
        $drive      = $config['default'] ?? null;
        $connection = $config['connections'][$drive] ?? null;
        $host       = $connection['host'] ?? null;
        $username   = $connection['username'] ?? null;
        $password   = $connection['password'] ?? null;
        $dbName     = $connection['db_name'] ?? null;
        $port       = $connection['port'] ?? null;
        Log::info('Connecting to the database');
        try {
            $this->pdo = new PDO("$drive:host=$host;port=$port;dbname=$dbName", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
            Log::info('Connected to the database');
        } catch ( PDOException $e ) {
            Log::error($e->getMessage());
        }
    }

    public function rollbackMigrations(): void {
        if (!isset($this->pdo)) {
            $this->connectToDatabase();
        }
        $this->handleRollbackMigrations($this->pdo);
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}