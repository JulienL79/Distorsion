<?php

class ProfileController {
    
    private $userManager;
    private $serverManager;
    
    public function __construct ($pdo) {
        $this->userManager = new UserManager($pdo);
        $this->serverManager = new ServerManager($pdo);
    }
    
    public function handleRequest() {
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case 'login':
                $this->login(); // Affiche le formulaire de connexion
                break;
            case 'logout':
                $this->logout(); // Affiche le formulaire de connexion
                break;
            case 'settings':
                $this->setting(); //Affiche les paramètres
                break;
            case 'sign-up':
                $this->signUp(); // Affiche le formulaire pour créer un nouvel utilisateur
                break;
            case 'edit-user':
                $this->editUser(); // Affiche le formulaire de connexion
                break;
            case 'update-password':
                $this->updatePassword(); // Affiche le formulaire de connexion
                break;
            case 'delete-user':
                $this->deleteUser(); // Affiche le formulaire de connexion
                break;
            default:
                $this->display(); // Affiche la vue du profil par défaut
                break;
        }
    }
    
    public function display() {
        
        $action = $_GET['action'] ?? null;

        if(!isset($_SESSION['logged'])){
            header("Location: index.php?page=profile&action=login");
            exit();
        } else {
            $sub_template = './views/profile/main_content/profile_info.phtml';
        }
        
        $template = './views/profile/profile.phtml';
        require './views/layout.phtml';
    }
    
    public function login() {
        
        $error = null;
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
            $pseudo = $_POST['pseudo'];
            $password = $_POST['password'];
            
            $user = $this->userManager->getUserByPseudo($pseudo);
            
            if ($user) {
            // Vérifier si le mot de passe saisi correspond au hash stocké
                if (password_verify($password, $user->getPassword())) {
                    
                    $_SESSION['logged'] = true;
                    $_SESSION['user']['id'] = $user->getId();
                    $_SESSION['user']['pseudo'] = $user->getPseudo();
                    $_SESSION['user']['firstname'] = $user->getFirstname();
                    $_SESSION['user']['lastname'] = $user->getLastname();
                    $_SESSION['user']['email'] = $user->getEmail();
                    $_SESSION['user']['tel'] = $user->getTel();
                    $_SESSION['user']['birthdate'] = $user->getBirthdate();
                    $_SESSION['user']['address_number'] = $user->getAddressNumber();
                    $_SESSION['user']['address_street'] = $user->getAddressStreet();
                    $_SESSION['user']['address_city'] = $user->getAddressCity();
                    $_SESSION['user']['address_postal_code'] = $user->getAddressPostalCode();
                    $_SESSION['user']['address_country'] = $user->getAddressCountry();
        
                    header('Location: index.php?page=profile');
                    exit();
                } else {
                    $error = 'Mot de passe invalide';
                }
            } else {
                $error = 'Pseudo invalide';
            }
        }
        
        $sub_template = './views/profile/main_content/login.phtml';
        $template = './views/profile/profile.phtml';
        require './views/layout.phtml';
    }
    
    public function logout() {
        
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }
    
    public function signUp() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']);
            $pseudo = trim($_POST['pseudo']);
            $email = trim($_POST['email']);
            $tel = trim($_POST['tel']);
            $birthdate = $_POST['birthdate'];
            $addressNumber = $_POST['address-number'];
            $addressStreet = trim($_POST['address-street']);
            $addressCity = trim($_POST['address-city']);
            $addressPostalCode = trim($_POST['address-postal-code']);
            $addressCountry = trim($_POST['address-country']);
            $password = $_POST['password'];
            $passwordConfirm = $_POST['password-confirm'];
            $registrationDate = new DateTime();
            $formattedDate = $registrationDate->format('Y-m-d H:i:s');
            
            // Initialisation des erreurs
            $errors = [];
            
            $smallNameRegex = "/^[a-zA-Z0-9.\- ]{1,50}$/";
            $nameRegex = "/^[a-zA-Z0-9.\- ]{1,100}$/";
            $charMaxRegex ="/^[a-zA-Z0-9.\- ]{1,255}$/";
            $emailRegex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
            $telRegex ="/^(\+?\d{1,4})?\s?\d{7,15}$/";
            $birthdateRegex ="/^\d{4}-\d{2}-\d{2}$/";
            $addressNumberRegex = "/^[1-9]\d*$/";
            $postalCodeRegex ="/^\d{5}$/";
            $passwordRegex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_])\S{8,16}$/";
            
            if (!preg_match($nameRegex, $firstname)) {
                $errors['firstname'] = "Le prénom est invalide.";
            }
            
            if (!preg_match($nameRegex, $lastname)) {
                $errors['lastname'] = "Le nom est invalide.";
            }
            
            if (!preg_match($smallNameRegex, $pseudo)) {
                $errors['pseudo'] = "Le pseudo est invalide.";
            }
            
            if ($this->userManager->getUserByPseudo($pseudo)) {
                $errors['pseudo'] = "Le pseudo est déjà utilisée.";
            }
            
            if (!preg_match($emailRegex, $email)) {
                $errors['email'] = "L'email est invalide.";
            }
            
            if ($this->userManager->getUserByEmail($email)) {
                $errors['email'] = "L'email est déjà utilisée.";
            }
            
            if (!preg_match($telRegex, $tel)) {
                $errors['tel'] = "Le numéro de téléphone est invalide.";
            }
            
            if (!preg_match($birthdateRegex, $birthdate)) {
                $errors['birthdate'] = "La date de naissance doit être au format JJ/MM/AAAA.";
            }
            
            if (!preg_match($addressNumberRegex, $addressNumber)) {
                $errors['address-number'] = "Le numéro de rue est invalide.";
            }
            
            if (!preg_match($charMaxRegex, $addressStreet)) {
                $errors['address-street'] = "L'adresse est invalide";
            }
            
            if (!preg_match($charMaxRegex, $addressCity)) {
                $errors['address-city'] = "L'adresse est invalide";
            }
            
            if (!preg_match($postalCodeRegex, $addressPostalCode)) {
                $errors['address-postal-code'] = "Le code postal est invalide.";
            }
            
            if (!preg_match($charMaxRegex, $addressCountry)) {
                $errors['address-country'] = "Doit être inférieur à 255 caractères";
            }
            
            if (!preg_match($passwordRegex, $password)) {
                $errors['password'] = "Le mot de passe doit avoir une longueur de 8 à 16 caractères, contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.";
            }
            
            if ($password !== $passwordConfirm) {
                $errors['password-confirm'] = "Les mots de passe ne correspondent pas.";
            }
            
            if (empty($errors)) {
        
                $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $newUser = $this->userManager->createUser([
                                'firstname' => $firstname,
                                'lastname' => $lastname,
                                'email' => $email,
                                'pseudo' => $pseudo,
                                'tel' => $tel,
                                'birthdate' => $birthdate,
                                'address_number' => $addressNumber,
                                'address_street' => $addressStreet,
                                'address_city' => $addressCity,
                                'address_postal_code' => $addressPostalCode,
                                'address_country' => $addressCountry,
                                'password' => $hashPassword,
                                'registration_date' => $formattedDate,
                            ]);
                
                header('Location: index.php?page=profile');
                exit();
            }
        }
        
        $sub_template = './views/profile/main_content/sign_up.phtml';
        $template = './views/profile/profile.phtml';
        require './views/layout.phtml';
    }
    
    public function setting() {
        $action = $_GET['action'] ?? null;
        $sub_template = './views/profile/main_content/setting.phtml';
        $template = './views/profile/profile.phtml';
        require './views/layout.phtml';
    }
    
    public function deleteUser() {
        
        $action = $_GET['action'] ?? null;
        
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=profile');
            exit();
            
        }
        
        
            
        $currentUserId = $_SESSION['user']['id'];
        $user = $this->userManager->getUserById($currentUserId);
        $servers = $this->userManager->getAllServersForUser($currentUserId);
        
        foreach($servers as $server) {
            $adminCount = 0;
            $serverId = $server->getId();
            $currentRole = $this->serverManager->getRoleOnServer($currentUserId, $serverId);
            $membersRoles = $this->serverManager->getUserRolesAndJoinedAt($serverId);
            foreach ($membersRoles as $userId => $userInfo) {
                if ($userInfo['role'] === 'admin') {
                    $adminCount++;
                }
            }
            if ($adminCount === 1 && $currentRole === 'admin') {
                $error = 'Vous ne pouvez pas supprimer votre compte si vous êtes le seul admin sur un serveur';
                $sub_template = './views/profile/main_content/setting.phtml';
                $template = './views/profile/profile.phtml';
                require './views/layout.phtml';
                return false;
            }
        }
        
        $isDeleted = $this->userManager->deleteUser($currentUserId);
        
        if ($isDeleted) {
            session_unset();
            session_destroy();
            header('Location: index.php');
            exit();
        } else {
            header('Location: index.php?page=profile');
            exit();
        }
    }
    
    public function editUser() {
        
        $action = $_GET['action'] ?? null;
        
        if (!isset($_SESSION['user'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=profile');
            exit();
            
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
            $newFirstname = trim($_POST['firstname']);
            $newLastname = trim($_POST['lastname']);
            $newPseudo = trim($_POST['pseudo']);
            $newEmail = trim($_POST['email']);
            $newTel = trim($_POST['tel']);
            $newBirthdate = $_POST['birthdate'];
            $newAddressNumber = $_POST['address-number'];
            $newAddressStreet = trim($_POST['address-street']);
            $newAddressCity = trim($_POST['address-city']);
            $newAddressPostalCode = trim($_POST['address-postal-code']);
            $newAddressCountry = trim($_POST['address-country']);
            
            // Initialisation des erreurs
            $errors = [];
            
            $smallNameRegex = "/^[a-zA-Z0-9.\- ]{1,50}$/";
            $nameRegex = "/^[a-zA-Z0-9.\- ]{1,100}$/";
            $charMaxRegex ="/^[a-zA-Z0-9.\- ]{1,255}$/";
            $emailRegex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
            $telRegex ="/^(\+?\d{1,4})?\s?\d{7,15}$/";
            $birthdateRegex ="/^\d{4}-\d{2}-\d{2}$/";
            $addressNumberRegex = "/^[1-9]\d*$/";
            $postalCodeRegex ="/^\d{5}$/";
            
            if (!preg_match($nameRegex, $newFirstname)) {
                $errors['firstname'] = "Le prénom est invalide.";
            }
            
            if (!preg_match($nameRegex, $newLastname)) {
                $errors['lastname'] = "Le nom est invalide.";
            }
            
            if (!preg_match($smallNameRegex, $newPseudo)) {
                $errors['pseudo'] = "Le pseudo est invalide.";
            }
            
            if ($this->userManager->getUserByPseudo($newPseudo) && $newPseudo !== $_SESSION['user']['pseudo']) {
                $errors['pseudo'] = "Le pseudo est déjà utilisée.";
            }
            
            if (!preg_match($emailRegex, $newEmail)) {
                $errors['email'] = "L'email est invalide.";
            }
            
            if ($this->userManager->getUserByEmail($newEmail) && $newEmail !== $_SESSION['user']['email']) {
                $errors['email'] = "L'email est déjà utilisée.";
            }
            
            if (!preg_match($telRegex, $newTel)) {
                $errors['tel'] = "Le numéro de téléphone est invalide.";
            }
            
            if (!preg_match($birthdateRegex, $newBirthdate)) {
                $errors['birthdate'] = "La date de naissance doit être au format JJ/MM/AAAA.";
            }
            
            if (!preg_match($addressNumberRegex, $newAddressNumber)) {
                $errors['address-number'] = "Le numéro de rue est invalide.";
            }
            
            if (!preg_match($charMaxRegex, $newAddressStreet)) {
                $errors['address-street'] = "L'adresse est invalide";
            }
            
            if (!preg_match($charMaxRegex, $newAddressCity)) {
                $errors['address-city'] = "L'adresse est invalide";
            }
            
            if (!preg_match($postalCodeRegex, $newAddressPostalCode)) {
                $errors['address-postal-code'] = "Le code postal est invalide.";
            }
            
            if (!preg_match($charMaxRegex, $newAddressCountry)) {
                $errors['address-country'] = "Doit être inférieur à 255 caractères";
            }
            
            if (empty($errors)) {
                
                $isModified = $this->userManager->updateUser([
                                'id' => $_SESSION['user']['id'],
                                'firstname' => $newFirstname,
                                'lastname' => $newLastname,
                                'email' => $newEmail,
                                'pseudo' => $newPseudo,
                                'tel' => $newTel,
                                'birthdate' => $newBirthdate,
                                'address_number' => $newAddressNumber,
                                'address_street' => $newAddressStreet,
                                'address_city' => $newAddressCity,
                                'address_postal_code' => $newAddressPostalCode,
                                'address_country' => $newAddressCountry,
                            ]);
                            
                if ($isModified) {
                    $user = $this->userManager->getUserById($_SESSION['user']['id']);
                    $_SESSION['user']['pseudo'] = $user->getPseudo();
                    $_SESSION['user']['firstname'] = $user->getFirstname();
                    $_SESSION['user']['lastname'] = $user->getLastname();
                    $_SESSION['user']['email'] = $user->getEmail();
                    $_SESSION['user']['tel'] = $user->getTel();
                    $_SESSION['user']['birthdate'] = $user->getBirthdate();
                    $_SESSION['user']['address_number'] = $user->getAddressNumber();
                    $_SESSION['user']['address_street'] = $user->getAddressStreet();
                    $_SESSION['user']['address_city'] = $user->getAddressCity();
                    $_SESSION['user']['address_postal_code'] = $user->getAddressPostalCode();
                    $_SESSION['user']['address_country'] = $user->getAddressCountry();
                }
                            
                header('Location: index.php?page=profile');
                exit();
            }
        }
        
        $sub_template = './views/profile/main_content/profile_info.phtml';
        $template = './views/profile/profile.phtml';
        require './views/layout.phtml';
    }
    
    public function updatePassword() {
        
        $action = $_GET['action'] ?? null;
        
        if (!isset($_SESSION['user'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=profile');
            exit();
            
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->userManager->getUserById($_SESSION['user']['id']);
            $pastPassword = $_POST['past-password'];
            $password = $_POST['password'];
            $passwordConfirm = $_POST['password-confirm'];
            
            // Initialisation des erreurs
            $errors = [];
            
            $passwordRegex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_])\S{8,16}$/";
            
            if(!password_verify($pastPassword, $user->getPassword())) {
                $errors['past-password'] = "Veuillez saisir correctement l'ancien mot de passe";
            }
            
            if (!preg_match($passwordRegex, $password)) {
                $errors['password'] = "Le mot de passe doit avoir une longueur de 8 à 16 caractères, contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.";
            }
            
            if ($password !== $passwordConfirm) {
                $errors['password-confirm'] = "Les mots de passe ne correspondent pas.";
            }
            
            if (empty($errors)) {
        
                $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $isModified = $this->userManager->updatePassword([
                                'id' => $_SESSION['user']['id'],
                                'password' => $hashPassword,
                            ]);
                
                if($isModified) {
                    $action = null;
                    $success = 'Modification du mot de passe réussie';
                } else {
                    $error = 'La modification a échouée';
                }
            }
        }
        
        $sub_template = './views/profile/main_content/setting.phtml';
        $template = './views/profile/profile.phtml';
        require './views/layout.phtml';
    }
}


?>