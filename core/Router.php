<?php

class Router {
    
    private $pdo;
    
    public function __construct ($pdo) {
        $this->pdo = $pdo;
    }
    
    public function routeRequest() {
        
        $page = $_GET['page'] ?? 'profile';
        $action = $_GET['action'] ?? 'display';
        
        switch($page){
            case 'home':
                $controller = new HomeController($this->pdo);
                $this->handleHomeController($controller, $action);
                break;
                
            case 'profile':
                $controller = new ProfileController($this->pdo);
                $this->handleProfileController($controller, $action);
                break;
            
            default :
                echo "Page not found";
                break;
        }
    }
    
    private function handleHomeController($controller, $action) {
        $controller->handleRequest();
    }
    
    private function handleProfileController($controller, $action) {
        $controller->handleRequest();
    }
}

?>