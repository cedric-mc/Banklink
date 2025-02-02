# Projet Banklink

## Description

Portail Web pour la gestion de paiement par cartes bancaires.

Il y a 3 types d'utilisateurs :

Rôle | Description | Nom dans la base de données
-----|-------------|----------------------------
Admin | Activer la création ou suppression d'un compte | admin
PO | Valider la création ou suppression d'un compte, Consulter les données de tous les clients | product-owner
Commerçant | Uniquement consulter ses données | client

## Installation

### Prérequis

- Serveur web (Apache, Nginx, ...)
- PHP 7.0 ou supérieur
- MySQL 5.7 ou supérieur
- Composer

### Installation du projet

1 - Cloner le projet

2 - Installer les dépendances avec la commande :

```bash
composer install
```

3 - Importer le fichier SQL `banklink.sql` dans votre base de données :
Attention : La base de données ne doit pas contenir des tables avec les noms suivants : `UTILISATEUR`, `CLIENT`, `REMISE`, `MOTIFS_IMPAYES`, `CLIENT_TEMP`

4 - Dupliquer le fichier `conf.bkp.php` et le renommer `conf.php` se trouvant dans le répertoire `includes` à la racine. Modifier les informations de connexion à la base de données dans ce fichier, `$utilisateur`, `$motdepasse`, `$serveur`, `$bdd`

## Utilisation

<!-- ### Guide utilisateur

Guide d'utilisateur du site web : [Guide utilisateur](https://github.com/) -->

### Comptes utilisables

Rôle | Login | Mot de Passe
-----|-------|-------------
Admin | `admin` | `admin`
PO | `productowner` | `productowner`
Commerçant | `apple` | `apple`
Commerçant | `airbus` | `airbus`
Commerçant | `stellantis` | `stellantis`
Commerçant | `nike` | `nike`

### Comptes clients désactivés

Commerçant | Login | Mot de Passe
-----------|-------|-------------
TotalEnergies | `total` | `total`
Test1 | `test1` | `test1`
Test2 | `test2` | `test2`

## Équipe

Scrum Master : [@Ahmed-Sarboudine](https://github.com/Ahmed-Sarboudine)

Technical Leader : [@cedric-mc](https://github.com/cedric-mc)

Developer : [@dmenoret](https://github.com/dmenoret) [@Ahmed-Sarboudine](https://github.com/Ahmed-Sarboudine) [@cedric-mc](https://github.com/cedric-mc)
