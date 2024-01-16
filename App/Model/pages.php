<?php
namespace App\Model;

use App\Model\MainModel;
use PDO;
use PDOException;
class pages extends MainModel {
    protected string $primaryKey = "id";
    protected string $tableName = "pages";
    
    public function isAdded($page_link): bool
    {
        $query = "select * from pages WHERE link= :link";
        $stmt = $this->db->prepare($query);
        $stmt ->execute([':link' => $page_link]);
        return $stmt->rowCount();
    }
    public function selectWithLink($page_link): bool
    {
        $query = "select * from pages WHERE link= :link";
        $stmt = $this->db->prepare($query);
        $stmt ->execute([':link' => $page_link]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function selectWithUserID($user_id)
    {
        $query = "select * from ".$this->tableName . " WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['user_id'=>$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function insertNewPage($page_link,$user_id): bool
    {
        if (!($this->isAdded($page_link))){
            $query ="insert into pages(user_id,link) value (:user_id,:link)";
            $stmt = $this->db ->prepare($query);
            $stmt -> execute(['user_id'=>$user_id,'link'=>$page_link]);
            return $stmt ->rowCount();
        }else{
            return false;
        }
    }
    public function lastInsertID()
    {
        return $this->db->lastInsertID();
    }
   

}