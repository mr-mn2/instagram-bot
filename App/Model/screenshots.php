<?php
namespace App\Model;

use App\Model\MainModel;
use PDO;

class screenshots extends MainModel
{
    protected string $primaryKey = "id";
    protected string $tableName = "screenshots";

    public function selectWithMsgID($msgID)
    {
        $query = "select * from screenshots WHERE msg_id= :msgID";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':msgID' => $msgID]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function selectWithUserID($user_id)
    {
        $query = "select * from " . $this->tableName . " WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function insertNewScreenshot($sender, $pic, $description): bool
    {
        $query = "insert into screenshots(sender_id,pic,description) value (:sender_id,:pic,:description)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['sender_id' => $sender, 'pic' => $pic, 'description' => $description]);
        return $stmt->rowCount();

    }
    public function lastInsertID()
    {
        return $this->db->lastInsertID();
    }
   

}
