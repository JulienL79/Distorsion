<?php

class HomeController {
    private $userManager;
    private $categoryManager;
    private $saloonManager;
    private $messageManager;
    private $serverManager;
    
    public function __construct ($pdo) {
        $this->userManager = new UserManager($pdo);
        $this->categoryManager = new CategoryManager($pdo);
        $this->saloonManager = new SaloonManager($pdo);
        $this->messageManager = new MessageManager($pdo);
        $this->serverManager = new ServerManager($pdo);
    }
    
    //******* GERE L'AFFECTATION DES FUNCTIONS ET ATTRIBUTS LES ELEMENTS A S_SESSION **********
    
    public function handleRequest() {
        $action = $_GET['action'] ?? null;
        $serverId = $_GET['server-id'] ?? null;
        $categoryId = $_GET['category-id'] ?? null;
        $saloonId = $_GET['saloon-id'] ?? null;
        $isOnServer = false;
        
        if ($serverId) {
            $isOnServer = $this->validateAndSetServer($serverId);
        } else if (isset($_SESSION['server']['id'])) {
            $isOnServer = $this->validateAndSetServer($_SESSION['server']['id']);
        }
        
        if ($isOnServer) {
            
            if($categoryId) {
                $category = $this->categoryManager->getCategoryById($categoryId);
                
                if($category) {
                    $_SESSION['category']['id'] = $categoryId;
                    $_SESSION['category']['name'] = $category->getName();
                }
            }
            
            if($saloonId) {
                $saloon = $this->saloonManager->getSaloonById($saloonId);
                
                if($saloon) {
                    $_SESSION['saloon']['id'] = $saloonId;
                    $_SESSION['saloon']['name'] = $saloon->getName();
                }
            }
        }

        switch ($action) {
            case 'filter':
                $this->toggleFilter();
                break;
            case 'new-server':
                $this->display(); // Affiche le formulaire pour une nouvelle catégorie
                break;
            case 'add-server':
                $this->addServer();
                break;
            case 'join-server':
                $this->joinServer();
                break;
            case 'update-server':
                $this->updateServer();
                break;
            case 'delete-server':
                $this->deleteServer();
                break;
            case 'edit-member':
                $this->display();
                break;
            case 'update-member':
                $this->updateMember();
                break;
            case 'delete-member':
                $this->deleteMember();
                break;
            case 'new-category':
                $this->display(); // Affiche le formulaire pour une nouvelle catégorie
                break;
            case 'add-category':
                $this->addCategory();
                break;
            case 'update-category':
                $this->updateCategory();
                break;
            case 'delete-category':
                $this->deleteCategory();
                break;
            case 'new-saloon':
                $this->display(); // Affiche le formulaire pour un nouveau salon
                break;
            case 'add-saloon':
                $this->addSaloon();
                break;
            case 'update-saloon':
                $this->updateSaloon();
                break;
            case 'delete-saloon':
                $this->deleteSaloon();
                break;
            case 'add-message':
                $this->addMessage();
                break;
            default:
                $this->display(); // Affiche la vue principale par défaut
                break;
        }
    }
    
    public function display(string $error = null) {

        if(!isset($_SESSION['logged'])){
            header("Location: index.php?page=profile&action=login");
            exit();
        }
        
        $errorToShow = $error;
        $action = $_GET['action'] ?? null;
        $currentUserId = $_SESSION['user']['id'] ?? null;
        $currentUserRole = $_SESSION['user']['role'] ?? null;
        $serverId = $_SESSION['server']['id'] ?? null;
        $serverName = $_SESSION['server']['name'] ?? null;
        $serverStatus = $_SESSION['server']['status'] ?? null;
        $members = $_SESSION['server']['members'] ?? null;
        $membersRolesAndDates = $_SESSION['server']['membersRolesAndDates'] ?? null;
        $adminCount = $_SESSION['server']['admin'] ?? null;
        
        $currentCategoryId = $_SESSION['category']['id'] ?? null;
        $currentSaloonId = $_SESSION['saloon']['id'] ?? null;
        $saloonName = $_SESSION['saloon']['name'] ?? null;
        
        
        
        if(!isset($_SESSION['filter']) || !$_SESSION['filter']) {
            $servers = $this->userManager->getPrivateServersForUser($currentUserId);
            $publicServers = $this->serverManager->getPublicServers();
        
            foreach($publicServers as $publicServer) {
                $servers[] = $publicServer;
            }
        } else {
            $servers = $this->userManager->getAllServersForUser($currentUserId);
        }
        
        
        $categories = [];
        
        if($serverId){
            $categories = $this->categoryManager->getCategoryByServerId($serverId);
        
            $_SESSION['server']['categories'] = $categories;
            
            $categorySaloons = [];
            
            foreach($categories as $category) {
                
                $categoryId = $category->getId();
                $saloons = $this->saloonManager->getSaloonByCategoryId($categoryId);
                $categorySaloons[$categoryId] = $saloons;
            }
            
            $_SESSION['category']['saloons'] = $categorySaloons;
    
            // Récupérer les messages si un salon est sélectionné
            $messages = [];
            if ($currentSaloonId) {
                $userRepository = $this->userManager;
                $messages = $this->messageManager->getMessagesBySaloonId($currentSaloonId);
            }
            
            $_SESSION['saloon']['messages'] = $messages;
        }
        
        if($action === 'edit-member') {
            $userTargetId = $_GET['user-id'];
            $userTarget = $this->userManager->getUserById($userTargetId);
            $memberPseudo = $userTarget->getPseudo();
            $memberRole = $this->serverManager->getRoleOnServer($userTargetId, $serverId);
        }
        
        // Inclure le template pour afficher la page
        $template = './views/home/home.phtml';
        require_once './views/layout.phtml';
    }
    
    //******* ATTRIBUT LES ELEMENTS A SESSION SI UN SERVEUR EST SELECTIONNE **********
    
    public function validateAndSetServer(int $serverId): bool {

        $server = $this->serverManager->getServerById($serverId);
        $serverName = $server->getName();
        $serverStatus = $server->getStatus();
        $currentUserId = $_SESSION['user']['id'];
        $isMember = false;
        $members = $this->serverManager->getUsers($serverId);
        $membersRolesAndDates = $this->serverManager->getUserRolesAndJoinedAt($serverId);
        $adminCount = 0;
        
        if(isset($_SESSION['user']['role'])) {
            unset($_SESSION['user']['role']);
        }
        
        // Compte le nombre d'admin du serveur
        foreach ($membersRolesAndDates as $userId => $userInfo) {
            if ($userInfo['role'] === 'admin') {
                $adminCount++;
            }
        }
        
        // Verifier si l'utilisateur fait partie des membres
        foreach ($members as $member) {
            $memberId = $member->getId();
            if ($memberId === $currentUserId) {
                $isMember = true;
                break;
            }
        }

        if ($isMember || $serverStatus === 'public') {
            $_SESSION['server']['id'] = $serverId;
            $_SESSION['server']['status'] = $serverStatus;
            $_SESSION['server']['name'] = $serverName;
            $_SESSION['server']['members'] = $members;
            $_SESSION['server']['admin'] = $adminCount;
            
            if(!empty($membersRolesAndDates)) {
                $_SESSION['server']['membersRolesAndDates'] = $membersRolesAndDates;
            }
            
            $currentUserRole = $this->serverManager->getRoleOnServer($currentUserId, $serverId);
            $_SESSION['user']['role'] = $currentUserRole;
            
            return true;
        } else {
            return false;
        }
    }
    
    // ******* FONCTION FILTRES DES SERVEURS ******************************
    
    public function toggleFilter(): void {
        
        unset($_SESSION['saloon']);
        unset($_SESSION['category']);
        unset($_SESSION['server']);
        unset($_SESSION['user']['role']);
        
        if(isset($_SESSION['filter'])) {
            if($_SESSION['filter']) {
                $_SESSION['filter'] = false;
            } else {
                $_SESSION['filter'] = true;
            }
        } else {
            $_SESSION['filter'] = true;
        }
        header('Location: index.php?page=home');
        exit;
    }
    
    // ******* FONCTIONS RELATIVES AUX SERVEURS ******************************
    
    public function addServer() {
        var_dump($_POST['name']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['password'])) {
            $serverName = trim($_POST['name']);
            $serverPassword = $_POST['password'];
            $serverStatus = $_POST['status'];
            
            if (!empty($serverName)) {
                
                $hashPassword = password_hash($serverPassword, PASSWORD_DEFAULT);

                $serverId = $this->serverManager->createServer(['name' => $serverName, 'password' => $hashPassword, 'status' => $serverStatus]);
                $this->userManager->addServerToUser($_SESSION['user']['id'], $serverId, 'admin');
                
                // Redirection ou mise à jour de la vue
                header('Location: index.php?page=home');
                exit();
            }
        }
    }
    
    public function joinServer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serverId = (int) $_POST['server-id'];
            $passwordSend = $_POST['password'];
            $serverTarget = $this->serverManager->getServerById($serverId);
                
            if ($serverTarget){
                
                $serverPassword = $serverTarget->getPassword();
            
                if(password_verify($passwordSend, $serverPassword)){
                    $isAdd = $this->userManager->addServerToUser($_SESSION['user']['id'], $serverId, 'user');
                    
                    if($isAdd) {
                        header('Location: index.php?page=home&server-id=' . $serverId);
                        exit();
                    } else {
                        $error = 'Serveur déjà rejoint';
                    }
                    
                } else {
                    $error = 'Mot de passe incorrect';
                }
            } else {
                $error = 'ID du serveur incorrect';
            }
        }
        $this->display($error);
        exit();
    }
    
    public function updateServer() {
        
        if ($_SESSION['user']['role'] !== 'admin' || !isset($_SESSION['server'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=home');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serverId = $_SESSION['server']['id'];
            $newServerName = trim($_POST['name']);
            $newServerPassword = $_POST['password'];
            $newServerStatus = $_POST['status'];
            $validStatus = ['public', 'private'];
            
            if (in_array($newServerStatus, $validStatus)) {
            
                if (!empty($serverPassword)) {
                    $hashPassword = password_hash($newServerPassword, PASSWORD_DEFAULT);
                    $this->serverManager->updateServerPassword([
                        'id' => $serverId,
                        'password' => $hashPassword,
                    ]);
                }
                
                $isModified = $this->serverManager->updateServer([
                    'id' => $serverId,
                    'name' => $newServerName,
                    'status' => $newServerStatus,
                ]);
                
                if ($isModified) {
                    header('Location: index.php?page=home&action=edit-server');
                    exit();
                } else {
                    header('Location: index.php?page=home');
                    exit();
                }
            
            }
            
            header('Location: index.php?page=home');
            exit();
        }
    }
    
    public function deleteServer() {
    
        if ($_SESSION['user']['role'] !== 'admin' || !isset($_SESSION['server'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=home');
            exit();
        }
        
        $serverId = $_SESSION['server']['id'];
        
        // Appeler la méthode deleteServer de ServerManager pour supprimer le serveur
        $isDeleted = $this->serverManager->deleteServer($serverId);

        if($isDeleted) {
            unset($_SESSION['server']);
            unset($_SESSION['category']);
            unset($_SESSION['saloon']);
            header('Location: index.php?page=home');
            exit();
        } else {
            header('Location: index.php?page=edit-server');
            exit();
        }
    }
    
    public function updateMember() {
        
        if ($_SESSION['user']['role'] !== 'admin' || !isset($_SESSION['server'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=home');
            exit();
        }
        
        if($SESSION['server']['admin'] == 1) {
            $error = 'Il doit rester impérativement 1 admin dans le serveur';
            $this->display($error);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serverId = $_SESSION['server']['id'];
            $userTargetId = (int) $_POST['user-id'];
            $newRole = $_POST['role'];
            $validRoles = ['user', 'admin'];
            
            if (in_array($newRole, $validRoles)) {
                $this->userManager->updateUserRole($userTargetId, $serverId, $newRole);
                
                header('Location: index.php?page=home&action=edit-server');
                exit();
            } else {
                header('Location: index.php?page=home');
                exit();
            }
        }
        
        
    }
    
    public function deleteMember() {
        
        $serverId = $_SESSION['server']['id'];
        $userTargetId = $_GET['user-id'] ?? $_SESSION['user']['id'];
        
        if ((!isset($_SESSION['user']['role']) || ($_SESSION['user']['role'] !== 'admin' && $userTargetId !== $_SESSION['user']['id'])) || !isset($_SESSION['server'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=home');
            exit();
        }
        
        if ($_SESSION['server']['admin'] === 1 && $userTargetId === $_SESSION['user']['id'] && $_SESSION['user']['role'] === 'admin') {
            $error = 'Il doit rester impérativement 1 admin dans le serveur';
            $this->display($error);
            exit();
        }

        $isDeleted = $this->userManager->removeServerFromUser($userTargetId, $serverId);
        
        if($isDeleted) {
            if($userTargetId === $_SESSION['user']['id']) {
                unset($_SESSION['server']);
                unset($_SESSION['category']);
                unset($_SESSION['saloon']);
                header('Location: index.php?page=home');
                exit();
            } else {
                header('Location: index.php?page=home&action=edit-server');
                exit();
            }
        }
        
        
    }
    
    // ******* FONCTIONS RELATIVES AUX CATEGORIES ******************************
    
    public function addCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['id-server-chat'])) {
            $categoryName = trim($_POST['name']);
            $idServerChat = $_POST['id-server-chat'];
            
            if (!empty($categoryName)) {
                // Ajouter la catégorie en utilisant CategoryManager
                $categoryId = $this->categoryManager->createCategory(['name' => $categoryName, 'id_server_chat' => $idServerChat]);
                
                $_SESSION['category']['id'] = $categoryId;
                $_SESSION['category']['name'] = $categoryName;
                // Redirection ou mise à jour de la vue
                header('Location: index.php?page=home');
                exit();
            }
        }
    }
    
    public function updateCategory() {
        
        if ($_SESSION['user']['role'] !== 'admin' || !isset($_SESSION['category'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=home');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            $categoryName = trim($_POST['name']);
            $categoryId = $_SESSION['category']['id'];
    
            if (!empty($categoryName)) {
                // Ajouter le salon en utilisant categoryManager
                $isModified = $this->categoryManager->updateCategory([
                    'name' => $categoryName,
                    'id' => $categoryId,
                ]);
                
                $_SESSION['category']['id'] = $categoryId;
                $_SESSION['category']['name'] = $categoryName;
                // Redirection ou mise à jour de la vue
                header('Location: index.php?page=home');
                exit();
            }
        }
    }
    
    public function deleteCategory() {
    
        if ($_SESSION['user']['role'] !== 'admin' || !isset($_SESSION['category'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=home');
            exit();
        }
        
        $categoryId = $_SESSION['category']['id'];
        
        // Appeler la méthode deleteServer de ServerManager pour supprimer le serveur
        $isDeleted = $this->categoryManager->deleteCategory($categoryId);
        
        if($isDeleted) {
            unset($_SESSION['category']);
            unset($_SESSION['saloon']);
            header('Location: index.php?page=home');
            exit();
        } else {
            header('Location: index.php?page=home');
            exit();
        }
    }
    
    // ******* FONCTIONS RELATIVES AUX SALONS ******************************
    
    public function addSaloon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['id-category'])) {
            $saloonName = trim($_POST['name']);
            $idServerChat = $_GET['server-id'];
            $categoryId = (int) $_POST['id-category'];
    
            if (!empty($saloonName) && $categoryId > 0) {
                // Ajouter le salon en utilisant SaloonManager
                $saloonId = $this->saloonManager->createSaloon([
                    'name' => $saloonName,
                    'id_category' => $categoryId,
                ]);
                
                $_SESSION['saloon']['id'] = $saloonId;
                $_SESSION['saloon']['name'] = $saloonName;
                // Redirection ou mise à jour de la vue
                header('Location: index.php?page=home');
                exit();
            }
        }
    }
    
    public function updateSaloon() {
        
        if ($_SESSION['user']['role'] !== 'admin' || !isset($_SESSION['saloon'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=home');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            $saloonName = trim($_POST['name']);
            $saloonId = $_SESSION['saloon']['id'];
    
            if (!empty($saloonName)) {
                // Ajouter le salon en utilisant SaloonManager
                $isModified = $this->saloonManager->updateSaloon([
                    'name' => $saloonName,
                    'id' => $saloonId,
                ]);
                
                $_SESSION['saloon']['id'] = $saloonId;
                $_SESSION['saloon']['name'] = $saloonName;
                // Redirection ou mise à jour de la vue
                header('Location: index.php?page=home');
                exit();
            }
        }
    }
    
    public function deleteSaloon() {
    
        if ($_SESSION['user']['role'] !== 'admin' || !isset($_SESSION['saloon'])) {
            // Redirection ou message d'erreur si l'utilisateur n'est pas admin
            header('Location: index.php?page=home');
            exit();
        }
        

        $saloonId = $_SESSION['saloon']['id'];
        
        // Appeler la méthode deleteServer de ServerManager pour supprimer le serveur
        $isDeleted = $this->saloonManager->deleteSaloon($saloonId);
        
        if($isDeleted) {
            unset($_SESSION['saloon']);
            header('Location: index.php?page=home');
            exit();
        } else {
            header('Location: index.php?page=home');
            exit();
        }
    }
    
    // ******* FONCTIONS RELATIVES AUX MESSAGES ******************************
    
    public function addMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'], $_POST['saloon-id'])) {
            $messageContent = trim($_POST['content']);
            $sendingDate = new DateTime();
            $formattedDate = $sendingDate->format('Y-m-d H:i:s');
    
            if (!empty($messageContent) && $_SESSION['saloon']['id'] > 0) {
                
                $this->messageManager->createMessage([
                    'content' => $messageContent,
                    'sending_date' => $formattedDate,
                    'id_saloon' => $_SESSION['saloon']['id'],
                    'id_user' => $_SESSION['user']['id']
                ]);
    
                // Redirection ou mise à jour de la vue
                header('Location: index.php?page=home');
                exit();
            }
        }
    }
}


?>
