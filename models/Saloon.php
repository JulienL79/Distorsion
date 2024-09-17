<?php

class Saloon {
    
    private int $id;
    private string $name;
    private int $id_category;
    
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


    public function getIdCategory(): int {
        return $this->id_category;
    }

    public function setIdCategory(int $id_category): void {
        $this->id_category = $id_category;
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
                case 'id_category':
                    $this->setIdCategory((int) $value);
                    break;
                default:
                    break;
            }
        }
    }
}

?>