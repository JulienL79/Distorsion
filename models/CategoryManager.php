<?php

class CategoryManager {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Méthode pour récupérer une catégorie par son ID
    public function getCategoryById(int $id): ?Category {
        $query = $this->db->prepare("SELECT * FROM category WHERE id = :id");
        $query->execute(['id' => $id]);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $category = new Category();
        $category->hydrate($data);
        return $category;
    }
    
    public function getCategoryByServerId(int $serverId): array {
        $query = $this->db->prepare("SELECT * FROM category WHERE id_server_chat = :id_server_chat ORDER BY name ASC");
        $query->execute(['id_server_chat' => $serverId]);
        
        $categories = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $category = new Category();
            $category->hydrate($data);
            $categories[] = $category;
        }
        return $categories;
    }
    
    public function getAllCategories(): array {
        $query = $this->db->query("SELECT * FROM category ORDER BY name ASC");
        
        $categories = [];
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $category = new Category();
            $category->hydrate($data);
            $categories[] = $category;
        }
        return $categories;
    }

    // Méthode pour créer une nouvelle catégorie
    public function createCategory(array $categoryData): int {
        $query = $this->db->prepare("INSERT INTO category (name, id_server_chat) VALUES (:name, :id_server_chat)");
        $query->execute($categoryData);
        return $this->db->lastInsertId();  // Retourne l'ID de la nouvelle catégorie
    }

    // Méthode pour mettre à jour une catégorie
    public function updateCategory(array $categoryData): bool {
        $query = $this->db->prepare("UPDATE category SET name = :name WHERE id = :id");
        $query->execute($categoryData);
        return $query->rowCount() > 0;  // Renvoie true si la mise à jour a eu lieu
    }

    // Méthode pour supprimer une catégorie
    public function deleteCategory(int $id): bool {
        $query = $this->db->prepare("DELETE FROM category WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->rowCount() > 0;  // Renvoie true si la suppression a été effectuée
    }
}

?>