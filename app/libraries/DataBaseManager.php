<?php

namespace App\Libraries;
use App\Libraries\Database;
use PDO;

class DataBaseManager
{
    private  $connection;

    public function __construct()
    {
        $db = Database::getInstance();
        $this->connection = $db->getConnection();
    }
    public function getConnection(){
        return  $this->connection;
    }

    public function insert(string $table, array $data): bool
    {

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));


        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($query);


        $i = 1;
        foreach ($data as $value) {
            $stmt->bindValue($i, $value);
            $i++;
        }

        // Exécuter la requête et retourner le résultat
        return $stmt->execute();
    }


    // Méthode de suppression générique
    public function delete(string $table, string $condition, int $param): bool
    {
        $query = "DELETE FROM $table WHERE $condition =:param";
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(":param", $param, PDO::PARAM_INT);
        return $stmt->execute();
    }


    public function update(string $table, array $data, string $whereColumn, $whereValue): bool
    {
        $setColumns = [];
        foreach ($data as $column => $value) {
            $setColumns[] = "\"$column\" = :$column"; // Ajout de guillemets pour PostgreSQL
        }

        $setClause = implode(", ", $setColumns);
        $query = "UPDATE \"$table\" SET $setClause WHERE \"$whereColumn\" = :whereValue";

        $stmt = $this->connection->prepare($query);

        // Liaison des valeurs en évitant bindParam
        foreach ($data as $column => $value) {
            $stmt->bindValue(":$column", $value); // bindValue au lieu de bindParam
        }
        $stmt->bindValue(":whereValue", $whereValue);

        // Affichage de la requête générée avec les valeurs
//        $finalQuery = $query;
//        foreach ($data as $column => $value) {
//            $finalQuery = str_replace(":$column", "'" . addslashes($value) . "'", $finalQuery);
//        }
//        $finalQuery = str_replace(":whereValue", "'" . addslashes($whereValue) . "'", $finalQuery);
//        echo "Query exécutée : " . $finalQuery . "\n";
//
//        echo "hlp";
        $stmt->execute();

        return ($stmt->rowCount() > 0); // Retourne vrai si des lignes ont été mises à jour
    }

    public function selectAll(string $table)
    {
        $query = "SELECT * FROM $table where archived = FALSE  ";
      
        $stmt = $this->connection->prepare($query);
        if ($stmt->execute()) {

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }


    
    public function selectAll_Archived(string $table): array |bool
    {
        $query = "SELECT * FROM $table where archived = 1";
        $stmt = $this->connection->prepare($query);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }

    public function selectBy(string $table, array $params = [])
    {
        $query = "SELECT * FROM $table";
        if (!empty($params)) {
            $conditions = [];
            foreach ($params as $param => $condition) {
                $conditions[] = "$param = :$param";
            }
            $cond =  implode(' AND ', $conditions);
            $query .= " WHERE " . $cond;
        }

        $stmt = $this->connection->prepare($query);
        if (!empty($params)) {
            foreach ($params as $param => $condition) {
                $stmt->bindValue(":$param", $condition);
            }
        }
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }




    public function getLastInsertId(): int
    {
        return (int)$this->connection->lastInsertId();
    }
}
