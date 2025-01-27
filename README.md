<img src="public/images/home.png" alt="CritiPixel" width="200" />

# Optimisation du site d'Ina Zaoui

Ina Zaoui est photographe professionnelle. Elle possède un site sur lequel elle partage ses photographies et depuis lequel elle promeut de jeunes photographes en partageant leurs clichés.

# Description 


- **Migration** : dernière version stable de Symfony (6.4.16).

- **Optimisation des performances** : correction des requêtes SQL, ajout de mécanismes de pagination.

- **Correction des anomalies** : gestion dynamique des connexions depuis la base de données et vérification des fichiers uploadés.

- **Ajout de nouvelles fonctionnalités pour la gestion de l'espace administrateur** : création d'une page de gestion des invités avec la possibilité pour l'administrateur de lister l'ensemble des invités, d'ajouter un nouvel invité, de révoquer ses accès et de supprimer un invité.

- **Implémentation des tests** : création de fixtures et implémentation de tests unitaires et fonctionnels.

- **Mise en place d'un pipeline d'intégration continue** : installation du projet, exécution de tests et des outils d'analyse statique.


# Installation

### Pré-requis :
* PHP >= 8.2
* Composer
* Extension PHP Xdebug
* Symfony 

### 1. Cloner le projet

Clonez le dépôt du projet avec les commandes suivantes :

```bash
git clone <URL_DU_DEPOT>
cd <NOM_DU_DOSSIER>
```

### 2. Installer les dépendances

Installez les dépendances du projet en utilisant Composer avec la commande suivante :

```bash
composer install
```

### 3. Configurer la base de données 

#### Paramétrer l’environnement :

Créez un fichier `.env.local` à la racine du projet avec la configuration suivante :

```bash
DATABASE_URL="postgresql://<utilisateur>:<mot_de_passe>@127.0.0.1:5432/ina_zaoui?serverVersion=16&charset=utf8"
```

*Note : Cette configuration doit être adaptée à votre environnement local en fonction du type de base de données utilisé et des paramètres d'accès.*

#### Créer la base de données :

```bash
php bin/console doctrine:database:create
```

#### Créer la structure de la base de données : 

```bash
php bin/console doctrine:schema:update --force  
```

#### Générer les fixtures :

```bash
php bin/console doctrine:fixtures:load --no-interaction --group=development
```

# Utilisation

### Fixtures :

- 1 administrateur.
- 150 médias.
- 5 albums pour l'administrateur : chaque album contient 30 médias.
- 100 invités : chaque invité est associé à 30 médias. 

### Structure du projet :

- **Entités** : base de données articulée autour des entités `Album`, `Media` et `User` (qui peut être un administrateur ou un invité).

- **Services** : utilisés pour centraliser la logique de pagination et de gestion des fichiers médias.

### Connexion à l'espace administrateur :

Pour accéder à l’espace administrateur, vous devez renseigner l'adresse e-mail et le mot de passe de l'utilisateur.

Exemple de connexion pour l'administrateur :

- email : `ina@zaoui.com`
- mot de passe : `password`

*Note : L'accès de l'utilisateur doit être activé dans la base de données avant qu'il puisse se connecter.*

### Gestion de l'espace administrateur :

#### Fonctionnalités de l'administrateur :

- Visualiser et gérer l'ensemble des médias.
- Créer, modifier ou supprimer des albums.
- Ajouter ou supprimer des médias pour l'ensemble des utilisateurs. 
- Ajouter, supprimer et contrôler l'accès des invités.

#### Fonctionnalités des invités :

- Ajouter un média et supprimer un média. 


# Tests

### Fixtures :

- 1 administrateur avec 5 albums contenant au total 10 médias.
- 10 invités (5 avec accès activé, 5 avec accès désactivé).
- 100 médias associés aux invités (50 pour les invités à accès activé, 50 pour les invités à accès désactivé). 

### 1. Configurer la base de données de test

#### Paramétrer l’environnement de test :

Créez un fichier `.env.test` à la racine du projet avec la configuration suivante :

```bash
KERNEL_CLASS='App\Kernel'
APP_SECRET='$ecretf0rt3st'
SYMFONY_DEPRECATIONS_HELPER=999999

DATABASE_URL="postgresql://<utilisateur>:<mot_de_passe>@127.0.0.1:5432/ina_zaoui?serverVersion=16&charset=utf8"
```
*Note : Cette configuration doit être adaptée à votre environnement local en fonction du type de base de données utilisé et des paramètres d'accès.*

#### Créer la base de données de test :

```bash
php bin/console doctrine:database:create --env=test
```

#### Créer la structure de la base de données de test :

```bash
php bin/console doctrine:schema:update --force --env=test   
```

#### Générer les fixtures dans l'environnement de test :

```bash
php bin/console doctrine:fixtures:load --env=test --no-interaction --group=tests --purge-with-truncate
```

### 2. Lancer les tests

```bash
vendor/bin/phpunit 
```