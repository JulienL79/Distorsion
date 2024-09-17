<?php

class Category {
    
    private int $id;
    private string $name;
    
    public function getId(): int {
        return $this->id;
    }
    
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }
    
    public function hydrate(array $data): void {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->setId((int) $value);
                    break;
                case 'name':
                    $this->setName($value);
                    break;
                default:
                    break;
            }
        }
    }
}

?>