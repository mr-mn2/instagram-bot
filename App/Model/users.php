<?php
namespace App\Model;

use App\Model\MainModel;
use PDO;
use PDOException;
class users extends MainModel {
    protected string $primaryKey = "user_id";
    protected string $tableName = "users";
    
    public function is_joined($user_id): bool
    {
        $query = "select * from users WHERE user_id=".$user_id;
        $stmt = $this->db->prepare($query);
        $stmt ->execute();
        return $stmt->rowCount();
    }
    public function insert_new_user($user_id,$name,$username,$position): bool
    {
        if (!($this->is_joined($user_id))){
            $query ="insert into users(user_id,name,username,position) value (:user_id,:name,:username,:position)";
            $stmt = $this->db ->prepare($query);
            $stmt -> execute(['user_id'=>$user_id,'name'=>$name,"username"=>$username,'position'=>$position]);
            return $stmt ->rowCount();
        }else{
            return false;
        }
    }
    public function selectByUsername($username)
    {
        $query = "select * from {$this->tableName} WHERE username=:username";
        $stmt = $this->db->prepare($query);
        $stmt ->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function SelectInviters($user_id)
    {
        $query = "select * from {$this->tableName} WHERE inviter=:user_id";
        $stmt = $this->db->prepare($query);
        $stmt ->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function countInviters($user_id)
    {
        $query = "select * from {$this->tableName} WHERE inviter=:user_id";
        $stmt = $this->db->prepare($query);
        $stmt ->execute(['user_id' => $user_id]);
        return $stmt->rowCount();
    }
    public function updateWithUsername($username,$field,$value)
    {
        $query = "update users set $field = '$value' where username = :username";
        $stmt = $this->db ->prepare($query);
        $stmt ->execute(['username' => $username]);
        return $stmt -> rowCount();
    }
    public function banUser($user_id,$field): int
    {
        $query = "update users set isBan = 1-".$field." where user_id=$user_id";
        $stmt = $this->db -> prepare($query);
        $stmt ->execute();
        return $stmt -> rowCount();

    }

}