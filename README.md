# Distorsion
Projet Distorsion

Description
Ce projet est une application de messagerie sécurisée développée entièrement en PHP sans framework. Elle permet aux utilisateurs de créer des serveurs de discussion, d’organiser des salons au sein de ces serveurs, et d’échanger des messages. Le projet met en place un système de gestion des rôles (utilisateur, administrateur), et permet une gestion sécurisée des messages et des utilisateurs.

Installation
Prérequis
PHP >= 8.0
MySQL >= 5.7
Apache ou un autre serveur web compatible
Extension PDO activée pour les interactions avec MySQL
Installation de la base de données
Importez la base de données en exécutant le fichier SQL fourni (database.sql), qui contient les définitions de tables suivantes :

Les tables incluses sont :

category : Gère les catégories des salons.
saloon : Gère les salons au sein des serveurs.
server_chat : Gère les serveurs de discussion.
server_chat_user : Lie les utilisateurs aux serveurs avec un rôle (admin ou user).
user : Contient les informations des utilisateurs.
message : Stocke les messages échangés dans les salons.

## Configuration
Clonez le projet
Modifiez le fichier de configuration core/database.php pour configurer vos informations de connexion à la base de données

## Fonctionnalités
### Gestion des utilisateurs
Inscription : Les utilisateurs peuvent s’inscrire en fournissant un pseudo, une adresse email, un mot de passe sécurisé, et d'autres informations personnelles.
Connexion : Authentification sécurisée via pseudo et mot de passe.
Rôles : Chaque utilisateur est assigné à un rôle : user ou admin au sein des serveurs de discussion.
### Création de serveurs et salons
Les utilisateurs peuvent créer des serveurs de discussion. Ils peuvent définir un serveur comme public ou privé.
Chaque serveur peut contenir plusieurs salons classés par catégorie.
Les administrateurs de serveurs peuvent inviter d'autres utilisateurs et les promouvoir en tant qu'administrateurs.
### Messagerie
Les utilisateurs peuvent envoyer des messages dans les salons. Ces messages sont associés à l'utilisateur et au salon dans lequel ils sont envoyés.
Les messages sont horodatés et peuvent être visualisés dans l'ordre chronologique avec des fonctionnalités de pagination si nécessaire.
### Sécurité
Mots de passe chiffrés : Les mots de passe des utilisateurs sont stockés de manière sécurisée à l’aide de password_hash().
Vérification des rôles : Seuls les administrateurs peuvent gérer les salons et inviter des utilisateurs.
Salons privés : L'accès à un serveur de discussion peut être restreint selon le rôle de l'utilisateur (public/privé).

# Structure du Projet
Le projet est organisé de manière simple et suit une architecture MVC sans framework.

## Routage
Le routage est basé sur un paramètre GET (page). Chaque route représente une page spécifique, par exemple :

http://localhost/index.php?page=login
Dans le fichier core/Router.php, vous définissez les pages disponibles avec leur contrôleur associé :

Le contrôleur charge ensuite la vue associée en fonction de la page demandée.

## Gestion des erreurs et validation des formulaires
Les contrôleurs reçoivent les données des formulaires via la superglobale $_POST.

Chaque modèle (par exemple, User, Message, Saloon) dispose d'une méthode validate() qui renvoie un tableau d'erreurs s'il y a des problèmes de validation.

## Fichiers et Répertoires Clés
controllers/ : Contient les fichiers qui traitent les requêtes utilisateurs, valident les données, et interagissent avec les modèles.
models/ : Représente les entités principales (User, ServerChat, Message, etc.).
views/ : Contient les fichiers .phtml utilisés pour afficher les pages à l'utilisateur.
core/ : Contient les fichiers de configuration (base de données, routage).

Ce README fournit une vue d'ensemble sur le projet, ses fonctionnalités, et les aspects techniques, tout en étant concis pour un projet sans framework.
