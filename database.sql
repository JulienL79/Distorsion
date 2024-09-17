SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `julienloiseau_distorsion` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `julienloiseau_distorsion`;

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `id_server_chat` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_server_chat` (`id_server_chat`),
  CONSTRAINT `category_ibfk_1` FOREIGN KEY (`id_server_chat`) REFERENCES `server_chat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sending_date` datetime DEFAULT NULL,
  `content` varchar(500) DEFAULT NULL,
  `id_user` int unsigned DEFAULT NULL,
  `id_saloon` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_saloon` (`id_saloon`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`id_saloon`) REFERENCES `saloon` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `saloon`;
CREATE TABLE `saloon` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `id_category` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_category` (`id_category`),
  CONSTRAINT `saloon_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `server_chat`;
CREATE TABLE `server_chat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('public','private') NOT NULL DEFAULT 'public',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `server_chat_user`;
CREATE TABLE `server_chat_user` (
  `id_user` int unsigned DEFAULT NULL,
  `id_server_chat` int DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id_server_chat` (`id_server_chat`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `server_chat_ibfk_1` FOREIGN KEY (`id_server_chat`) REFERENCES `server_chat` (`id`) ON DELETE CASCADE,
  CONSTRAINT `server_chat_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `birthdate` date DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `pseudo` varchar(50) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `address_number` int DEFAULT NULL,
  `address_street` varchar(255) DEFAULT NULL,
  `address_city` varchar(255) DEFAULT NULL,
  `address_postal_code` int DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `address_country` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `pseudo` (`pseudo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET foreign_key_checks = 1;