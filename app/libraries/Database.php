<?php
namespace App\Libraries;

class Database {
    private static $instance = null;
    private $dbh;
    private $stmt;

    // Constructeur privé pour empêcher l'instanciation externe
    private function __construct() {
        $host = "localhost";
        $port = 5432;
        $dbname = "nexus";
        $user = "postgres";
        $pass = "youcode";

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

        $options = [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Méthode pour récupérer l'instance unique
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Préparer une requête SQL
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Lier les valeurs aux paramètres
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            $type = match (true) {
                is_int($value) => \PDO::PARAM_INT,
                is_bool($value) => \PDO::PARAM_BOOL,
                is_null($value) => \PDO::PARAM_NULL,
                default => \PDO::PARAM_STR,
            };
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Exécuter la requête
    public function execute() {
        return $this->stmt->execute();
    }

    // Récupérer plusieurs résultats sous forme d'objets
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // Récupérer un seul résultat sous forme d'objet
    public function single() {
        $this->execute();
        return $this->stmt->fetch(\PDO::FETCH_OBJ);
    }

    // Récupérer le nombre de lignes affectées
    public function rowCount() {
        return $this->stmt->rowCount();
    }
}
