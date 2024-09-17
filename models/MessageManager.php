<?php

class MessageManager {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getMessageById(int $id): Message {
        $query = $this->db->prepare("SELECT * FROM user WHERE id = :id");
        $query->execute(['id' => $id]);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $message = new Message();
        $user->hydrate($data);
        return $message;
    }
    
    public function getMessagesBySaloonId(int $saloonId): array {
        $query = $this->db->prepare("SELECT * FROM message WHERE id_saloon = :id_saloon ORDER BY sending_date ASC");
        $query->execute(['id_saloon' => $saloonId]);
        $messages = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $message = new Message();
            $message->hydrate($data);
            $messages[] = $message;
        }
        return $messages;
    }
    
    public function createMessage(array $messageData): int {
        $query = $this->db->prepare("
            INSERT INTO message (content, sending_date, id_user, id_saloon)
            VALUES (:content, :sending_date, :id_user, :id_saloon)
        ");
        $query->execute($messageData);
        return $this->db->lastInsertId();
    }
}

?>