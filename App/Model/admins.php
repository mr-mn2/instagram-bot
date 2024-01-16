<?php
namespace App\Model;

use App\Model\MainModel;

class admins extends MainModel{
    protected string $primaryKey = "admin_user_id";
    protected string $tableName = "admins";
  
    public function insert_new_admin($user_id): bool
    {
        $query ="insert into {$this->tableName}(admin_user_id) value (:user_id)";
        $stmt = $this->db ->prepare($query);
        $stmt -> execute(['user_id'=>$user_id]);
        return $stmt ->rowCount();
    }
    public function updateRight($user_id,$field)
    {
        $query = "update {$this->tableName} set $field = 1- $field where {$this->primaryKey}=$user_id";
        $stmt = $this->db ->prepare($query);
        $stmt ->execute();
        return $stmt -> rowCount();
    }
   
  

}