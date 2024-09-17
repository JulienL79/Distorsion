<?php

class UserManager {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getUserById(int $id) {
        $query = $this->db->prepare("SELECT * FROM user WHERE id = :id");
        $query->execute(['id' => $id]);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if($data) {
            $user = new User();
            $user->hydrate($data);
            return $user;
        } else {
            return false;
        }
    }
    
    public function getUserByPseudo(string $pseudo) {
        $query = $this->db->prepare("SELECT * FROM user WHERE pseudo = :pseudo");
        $query->execute(['pseudo' => $pseudo]);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if($data) {
            $user = new User();
            $user->hydrate($data);
            return $user;
        } else {
            return false;
        }
    }
    
    public function getUserByEmail(string $email) {
        $query = $this->db->prepare("SELECT * FROM user WHERE email = :email");
        $query->execute(['email' => $email]);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if($data) {
            $user = new User();
            $user->hydrate($data);
            return $user;
        } else {
            return false;
        }
    }
    
    public function createUser(array $userData): int {
        $query = $this->db->prepare("INSERT INTO user (firstname, lastname, birthdate, pseudo, email, tel, password, registration_date, address_number, address_street, address_city, address_postal_code, address_country) VALUES (:firstname, :lastname, :birthdate, :pseudo, :email, :tel, :password, :registration_date, :address_number, :address_street, :address_city, :address_postal_code, :address_country)");
        $query->execute($userData);
        return $this->db->lastInsertId();
    }
    
    public function deleteUser(int $id): bool {
        $query = $this->db->prepare("DELETE FROM user WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->rowCount() > 0; // Renvoie true si un utilisateur a été supprimé, sinon false
    }
    
    public function updateUser(array $userData): bool {
        $query = $this->db->prepare("UPDATE user SET firstname = :firstname, lastname = :lastname, birthdate = :birthdate, pseudo = :pseudo, email = :email, tel = :tel, address_number = :address_number, address_street = :address_street, address_city = :address_city, address_postal_code = :address_postal_code, address_country = :address_country WHERE id = :id");
        $query->execute($userData);
        return $query->rowCount() > 0; // Renvoie true si l'utilisateur a été mis à jour, sinon false
    }
    
    public function updatePassword(array $userData): bool {
        $query = $this->db->prepare("UPDATE user SET password = :password WHERE id = :id");
        $query->execute($userData);
        return $query->rowCount() > 0; // Renvoie true si l'utilisateur a été mis à jour, sinon false
    }
    
    public function addServerToUser(int $userId, int $serverId, string $role): bool {
        
        // Vérifier si l'utilisateur est déjà connecté à ce serveur
        $query = $this->db->prepare("SELECT COUNT(*) FROM server_chat_user WHERE id_user = :id_user AND id_server_chat = :id_server_chat");
        $query->execute(['id_user' => $userId, 'id_server_chat' => $serverId]);
    
        $count = (int)$query->fetchColumn();
        
        if ($count === 0) {
            $query = $this->db->prepare("INSERT INTO server_chat_user (id_user, id_server_chat, role) VALUES (:id_user, :id_server_chat, :role)");
            $query->execute(['id_user' => $userId, 'id_server_chat' => $serverId, 'role' => $role]);
            return true;
        } else {
            return false;
        }
    }
    
    public function removeServerFromUser(int $userId, int $serverId) {
        $query = $this->db->prepare("DELETE FROM server_chat_user WHERE id_user = :id_user AND id_server_chat = :id_server_chat");
        $query->execute(['id_user' => $userId, 'id_server_chat' => $serverId]);
        
        return $query->rowCount() > 0;
    }
    
    public function getPrivateServersForUser(int $userId): array {
        $query = $this->db->prepare("SELECT s.* FROM server_chat s JOIN server_chat_user sc ON s.id = sc.id_server_chat WHERE sc.id_user = :id_user AND s.status = 'private' ORDER BY s.name ASC");
        $query->execute(['id_user' => $userId]);
        $servers = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $server = new Server();
            $server->hydrate($data);
            $servers[] = $server;
        }
        return $servers;
    }
    
    public function getAllServersForUser(int $userId): array {
        $query = $this->db->prepare("SELECT s.* FROM server_chat s JOIN server_chat_user sc ON s.id = sc.id_server_chat WHERE sc.id_user = :id_user ORDER BY s.name ASC");
        $query->execute(['id_user' => $userId]);
        $servers = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $server = new Server();
            $server->hydrate($data);
            $servers[] = $server;
        }
        return $servers;
    }
    
    public function updateUserRole(int $userId, int $serverId, string $newRole): bool {
        
    $query = $this->db->prepare('UPDATE server_chat_user SET role = :role WHERE id_user = :id_user AND id_server_chat = :id_server_chat');
    $query->execute([
        'id_user' => $userId,
        'id_server_chat' => $serverId,
        'role' => $newRole
    ]);
    return $query->rowCount() > 0;
    }
}

?>