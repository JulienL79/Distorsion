<?php

class Message {
    private int $id;
    private DateTime $sending_date;
    private string $content;
    private int $id_user;
    private int $id_saloon;
    
    public function getId(): int {
        return $this->id;
    }
    
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getSendingDate(): DateTime {
        return $this->sending_date;
    }

    public function setSendingDate(DateTime $sending_date): void {
        $this->sending_date = $sending_date;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function getIdUser(): int {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): void {
        $this->id_user = $id_user;
    }

    public function getIdSaloon(): int {
        return $this->id_saloon;
    }

    public function setIdSaloon(int $id_saloon): void {
        $this->id_saloon = $id_saloon;
    }
    
    public function hydrate(array $data): void {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->setId((int) $value);
                    break;
                case 'sending_date':
                    // Assurez-vous que la date est bien formatée pour créer un objet DateTime
                    $this->setSendingDate(new DateTime($value));
                    break;
                case 'content':
                    $this->setContent($value);
                    break;
                case 'id_user':
                    $this->setIdUser((int) $value);
                    break;
                case 'id_saloon':
                    $this->setIdSaloon((int) $value);
                    break;
                default:
                    break;
            }
        }
    }
}

?>