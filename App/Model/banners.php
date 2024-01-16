<?php
namespace App\Model;

use App\Model\MainModel;
use PDO;
use PDOException;
class banners extends MainModel {
    protected string $primaryKey = "id";
    protected string $tableName = "banners";

    public function isAdded($banner): bool
    {
        $query = "select * from banners WHERE banner= :banner";
        $stmt = $this->db->prepare($query);
        $stmt ->execute([':banner' => $banner]);
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
    public function insertNewBanner($banner,$description,$type): bool
    {
        if (!($this->isAdded($banner))){
            $query ="insert into banners(banner,description,type) value (:banner,:description,:type)";
            $stmt = $this->db ->prepare($query);
            $stmt -> execute(['banner'=>$banner,'description'=>$description,'type' => $type]);
            return $stmt ->rowCount();
        }else{
            return false;
        }
    }
    public function lastInsertID()
    {
        return $this->db->lastInsertID();
    }
    // public function selectByID($username)
    // {
    //     $query = "select * from {$this->tableName} WHERE username=:username";
    //     $stmt = $this->db->prepare($query);
    //     $stmt ->execute(['username' => $username]);
    //     return $stmt->fetch(PDO::FETCH_OBJ);
    // }
    // public function updateWithUsername($username,$field,$value)
    // {
    //     $query = "update users set $field = '$value' where username = :username";
    //     $stmt = $this->db ->prepare($query);
    //     $stmt ->execute(['username' => $username]);
    //     return $stmt -> rowCount();
    // }
    // public function banUser($user_id,$field): int
    // {
    //     $query = "update users set isBan = 1-".$field." where user_id=$user_id";
    //     $stmt = $this->db -> prepare($query);
    //     $stmt ->execute();
    //     return $stmt -> rowCount();

    // }

}