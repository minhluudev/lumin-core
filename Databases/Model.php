<?php

namespace Lumin\Databases;

use Lumin\App;
use Lumin\Databases\Interfaces\ModelInterface;
use Lumin\Databases\Traits\StaticModelTrait;
use Lumin\Helper;
use PDO;

class Model implements ModelInterface {
    use StaticModelTrait;

    protected PDO    $pdo;
    protected string $table;
    protected array  $fillable = [];
    protected array  $hidden   = [];

    public function __construct() {
        $this->pdo = App::$app->db->getConnection();
    }

    protected function getTableName(): string {
        if (isset($this->table)) {
            return $this->table;
        }

        $modelNames = explode('\\', get_class($this));
        $modelName  = end($modelNames);

        return Helper::convertToSnakeCaseAndPlural($modelName);
    }
}