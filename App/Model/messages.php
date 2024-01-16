<?php
namespace App\Model;

use App\Model\MainModel;
use PDO;

class messages extends MainModel
{
    protected string $primaryKey = "id";
    protected string $tableName = "messages";

    public function insertNewMessage($user_id, $msg_id_in_sender_chat, $msg_id_in_resever_chat)
    {
        $query = "insert into messages(sender,msg_id_in_sender_chat,msg_id_in_resever_chat) value (:sender,:msg_id_in_sender_chat,:msg_id_in_resever_chat)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['sender' => $user_id, 'msg_id_in_sender_chat' => $msg_id_in_sender_chat, 'msg_id_in_resever_chat' => $msg_id_in_resever_chat]);
        return $stmt->rowCount();

    }
    public function selectByMessageID($msgID)
    {
        $query = "select * from {$this->tableName} WHERE msg_id_in_resever_chat=".$msgID;
        $stmt = $this->db->prepare($query);
        $stmt ->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

}
