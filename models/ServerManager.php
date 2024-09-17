<?php

class ServerManager {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getServerById(int $id) {
        $query = $this->db->prepare("SELECT * FROM server_chat WHERE id = :id");
        $query->execute(['id' => $id]);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if($data) {
            $server = new Server();
            $server->hydrate($data);
            return $server;
        } else {
            return false;
        }
    }
    
    public function getPublicServers(): array {
        $query = $this->db->query("SELECT * FROM server_chat WHERE status = 'public' ORDER BY name ASC");
        
        $servers = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $server = new Server();
            $server->hydrate($data);
            $servers[] = $server;
        }
        return $servers;
    }
    
    function getRoleOnServer(int $userId, int $serverId): ?string {
        $query = "SELECT role FROM server_chat_user WHERE id_user = :userId AND id_server_chat = :serverId";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':userId' => $userId,
            ':serverId' => $serverId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result['role'];
        } else {
            return null;
        }
    }
    
    public function getUsers(int $serverId): array {
        $query = $this->db->prepare("SELECT u.* FROM user u JOIN server_chat_user sc ON u.id = sc.id_user WHERE sc.id_server_chat = :id_server_chat");
        $query->execute(['id_server_chat' => $serverId]);
        
        $members = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $member = new User();
            $member->hydrate($data);
            $members[] = $member;
        }
        return $members;
    }
    
    public function getUserRolesAndJoinedAt(int $serverId): array {
        $query = $this->db->prepare("SELECT id_user, role, joined_at FROM server_chat_user WHERE id_server_chat = :id_server_chat");
        $query->execute(['id_server_chat' => $serverId]);
    
        $userRolesAndDates = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $userRolesAndDates[$data['id_user']] = [
                'role' => $data['role'],
                'joined_at' => $data['joined_at']
            ];
        }
        
        return $userRolesAndDates;
    }

    public function createServer(array $serverData): int {
        $query = $this->db->prepare("INSERT INTO server_chat (name, password, status) VALUES (:name, :password, :status)");
        $query->execute($serverData);
        return $this->db->lastInsertId();
    }

    public function updateServer(array $serverData): bool {
        $query = $this->db->prepare("UPDATE server_chat SET name = :name, status = :status WHERE id = :id");
        $query->execute($serverData);
        return $query->rowCount() > 0;  // Renvoie true si la mise à jour a eu lieu
    }
    
    public function updateServerPassword(array $serverData): bool {
        $query = $this->db->prepare("UPDATE server_chat SET password = :password WHERE id = :id");
        $query->execute($serverData);
        return $query->rowCount() > 0;  // Renvoie true si la mise à jour a eu lieu
    }

    // Méthode pour supprimer une catégorie
    public function deleteServer(int $id): bool {
        $query = $this->db->prepare("DELETE FROM server_chat WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->rowCount() > 0;  // Renvoie true si la suppression a été effectuée
    }
}

?>