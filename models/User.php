<?php

class User {
    private int $id;
    private string $firstname;
    private string $lastname;
    private string $pseudo;
    private DateTime $birthdate;
    private string $email;
    private string $tel;
    private string $password;
    private DateTime $registration_date;
    private string $address_number;
    private string $address_street;
    private string $address_city;
    private string $address_postal_code;
    private string $address_country;
    
    public function getId() {
        return $this->id;
    }
    
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function getFirstname(): string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void {
        $this->firstname = $firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void {
        $this->lastname = $lastname;
    }
    
    public function getPseudo(): string {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): void {
        $this->pseudo = $pseudo;
    }

    public function getBirthdate(): DateTime {
        return $this->birthdate;
    }

    public function setBirthdate(DateTime $birthdate): void {
        $this->birthdate = $birthdate;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getTel(): string {
        return $this->tel;
    }

    public function setTel(string $tel): void {
        $this->tel = $tel;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function getRegistrationDate(): DateTime {
        return $this->registration_date;
    }

    public function setRegistrationDate(DateTime $registration_date): void {
        $this->registration_date = $registration_date;
    }

    public function getAddressNumber(): string {
        return $this->address_number;
    }

    public function setAddressNumber(string $address_number): void {
        $this->address_number = $address_number;
    }

    public function getAddressStreet(): string {
        return $this->address_street;
    }

    public function setAddressStreet(string $address_street): void {
        $this->address_street = $address_street;
    }

    public function getAddressCity(): string {
        return $this->address_city;
    }

    public function setAddressCity(string $address_city): void {
        $this->address_city = $address_city;
    }

    public function getAddressPostalCode(): string {
        return $this->address_postal_code;
    }

    public function setAddressPostalCode(string $address_postal_code): void {
        $this->address_postal_code = $address_postal_code;
    }

    public function getAddressCountry(): string {
        return $this->address_country;
    }

    public function setAddressCountry(string $address_country): void {
        $this->address_country = $address_country;
    }
    
    // Méthodes métier spécifiques à l'utilisateur
    public function validateEmail() {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }
    
    public function hydrate(array $data): void {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->setId($value);
                    break;
                case 'firstname':
                    $this->setFirstname($value);
                    break;
                case 'lastname':
                    $this->setLastname($value);
                    break;
                case 'birthdate':
                    // Si vous avez besoin de convertir une chaîne de date en DateTime
                    $this->setBirthdate(new DateTime($value));
                    break;
                case 'pseudo':
                    $this->setPseudo($value);
                    break;
                case 'email':
                    $this->setEmail($value);
                    break;
                case 'tel':
                    $this->setTel($value);
                    break;
                case 'password':
                    $this->setPassword($value);
                    break;
                case 'registration_date':
                    // Conversion en DateTime si nécessaire
                    $this->setRegistrationDate(new DateTime($value));
                    break;
                case 'address_number':
                    $this->setAddressNumber($value);
                    break;
                case 'address_street':
                    $this->setAddressStreet($value);
                    break;
                case 'address_city':
                    $this->setAddressCity($value);
                    break;
                case 'address_postal_code':
                    $this->setAddressPostalCode($value);
                    break;
                case 'address_country':
                    $this->setAddressCountry($value);
                    break;
                default:
                    break;
            }
        }
    }
}

?>