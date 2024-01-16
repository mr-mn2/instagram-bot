<?php
namespace App\Model;
use PDO;
use PDOException;
abstract class MainModel{
    protected PDO $db;
    protected string $primaryKey = "user_id";
    protected string $tableName = "banners";
    public function __construct()
    {
        // list($hostName,$user,$pass,$dbName) = ["localhost","terrsstncp_mehty4","0610343521@View","terrsstncp_View"];
        // $this -> db = new PDO("mysql:host=$hostName;dbname=$dbName;charset=utf8mb4",$user,$pass);
        // $this -> db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        list($hostName,$user,$pass,$dbName) = ["localhost","viewmiie_mehty4","12345678#Qwert","viewmiie_DB"];//database information
        $this -> db = new PDO("mysql:host=$hostName;dbname=$dbName;charset=utf8mb4",$user,$pass);
        $this -> db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
    }
    public function select($user_id)
    {
        $query = "select * from {$this->tableName} WHERE {$this->primaryKey}=".$user_id;
        $stmt = $this->db->prepare($query);
        $stmt ->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function is_register($user_id)
    {
        $query = "select * from {$this->tableName} WHERE {$this->primaryKey}=".$user_id;
        $stmt = $this->db->prepare($query);
        $stmt ->execute();
        return $stmt->rowCount();
    }
    

    public function selectAll()
    {
        $query = "select * from ".$this->tableName;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function countRows()
    {
        $query = "select * from ".$this->tableName;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->rowCount();

    }

    public function update($id,$field,$value): bool
    {
        $query = "update {$this->tableName} set $field = '$value' where {$this->primaryKey}=$id";
        $stmt = $this->db ->prepare($query);
        $stmt ->execute();
        return $stmt -> rowCount();

    }
    public function lastInsertId(){
        return $this->db->lastInsertId();
    }
    public function delete($user_id): bool
    {
        $query = "delete from {$this->tableName} where {$this->primaryKey} = :user_id";
        $stmt = $this->db -> prepare($query);
        $stmt -> execute(['user_id' => $user_id]);
        return $stmt->rowCount();

    }
    public function deleteAll(): int
    {
        $query = "delete from {$this->tableName}";
        $stmt = $this->db -> prepare($query);
        $stmt -> execute();
        return $stmt->rowCount();

    }
}