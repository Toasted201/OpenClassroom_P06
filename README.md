# Projet OpenclassRooms : Développez de A à Z le site communautaire SnowTricks

## Description du projet

Dans le cadre de la formation Développeur d'application - PHP / Symfony d'OpenClassRooms, voici le projet n°6 : développer un site communautaire.

## Contexte
Jimmy Sweat est un entrepreneur ambitieux passionné de snowboard. 
Son objectif est la création d'un site collaboratif pour faire connaître ce sport auprès du grand public et aider à l'apprentissage des figures (tricks).
Il souhaite capitaliser sur du contenu apporté par les internautes afin de développer un contenu riche et suscitant l’intérêt des utilisateurs du site. 
Par la suite, Jimmy souhaite développer un business de mise en relation avec les marques de snowboard grâce au trafic que le contenu aura généré.

Pour ce projet, nous allons nous concentrer sur la création technique du site pour Jimmy.

## Compétences évaluées

- Prendre en main le framework Symfony
- Développer une application proposant les fonctionnalités attendues par le client
- Gérer une base de données MySQL ou NoSQL avec Doctrine
- Organiser son code pour garantir la lisibilité et la maintenabilité
- Prendre en main le moteur de templating Twig
- Respecter les bonnes pratiques de développement en vigueur
- Sélectionner les langages de programmation adaptés pour le développement de l’application


## Pour commencer

### Prérequis

- Php 7.4
- Composer 2.0
- Une base de données mySQL 5.7
- Git

### Installation

- Cloner le projet en local
- Exécuter la commande composer :
```bash
composer install
```
- Intégrer les données de démo : Exécuter la commande composer : 
```bash
composer run-script prepare-db --dev
```
- Identifiants de test :
pseudo : BobDoe 
mot de passe : passpass


### Paramétrage

Modifier les informations de connexion dans un fichier /.env.local à mettre à la racine du projet.
- Base de données : doctrine/doctrine-bundle
- Envoi des mails : symfony/mailer


## Fabriqué avec

* Visual Studio Code
* Twig Language
* PHP Sniffer & Beautifier
* PHP Intelephense
* MAMP
* Symnfony 5.2
* Doctrine
* bootswatch 4.5 
* twig 3.0
* phpunit-bridge 5.2

## Versions

- **V1.0** Première version
- **V1.1** Refactoring
- **V1.2** Modification texte message d’erreur
