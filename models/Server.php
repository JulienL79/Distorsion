<?php

class Server {
    
    private int $id;
    private string $name;
    private string $password;
    private string $status;
    
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
    
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }
    
    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
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
                case 'password':
                    $this->setPassword($value);
                    break;
                case 'status':
                    $this->setStatus($value);
                    break;
                default:
                    break;
            }
        }
    }
}

?>