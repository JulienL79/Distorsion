<?php

class SaloonManager {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Méthode pour récupérer un Saloon par son ID
    public function getSaloonById(int $id): ?Saloon {
        $query = $this->db->prepare("SELECT * FROM saloon WHERE id = :id");
        $query->execute(['id' => $id]);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $saloon = new Saloon();
        $saloon->hydrate($data);
        return $saloon;
    }
    
    public function getSaloonByCategoryId(int $categoryId): array {
        $query = $this->db->prepare("SELECT * FROM saloon WHERE id_category = :id_category ORDER BY name ASC");
        $query->execute(['id_category' => $categoryId]);
        
        $saloons = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $saloon = new Saloon();
            $saloon->hydrate($data);
            $saloons[] = $saloon;
        }
        return $saloons;
    }
    
    public function createSaloon(array $saloonData): int {
        $query = $this->db->prepare("INSERT INTO saloon (name, id_category) VALUES (:name, :id_category)");
        $query->execute($saloonData);
        return $this->db->lastInsertId();  // Retourne l'ID du nouveau saloon
    }

    public function updateSaloon(array $saloonData): bool {
        $query = $this->db->prepare("UPDATE saloon SET name = :name WHERE id = :id");
        $query->execute($saloonData);
        return $query->rowCount() > 0;  // Renvoie true si la mise à jour a été effectuée
    }
    
    public function deleteSaloon(int $id): bool {
        $query = $this->db->prepare("DELETE FROM saloon WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->rowCount() > 0;  // Renvoie true si une suppression a été effectuée
    }
}

?>