# Plateforme d'e-commerce pour Enfants (École/Collège)

Ce projet est une plateforme web conçue pour permettre aux enfants d'effectuer des achats auprès d'entreprises affiliées à leur école ou collège. Le système permet aux parents de superviser les dépenses de leurs enfants en consultant l'historique des transactions et les détails de consommation. Il facilite les interactions entre les enfants, leurs parents et les entreprises partenaires (fournisseurs/prestataires de services). Une interface administrateur supervise l'ensemble des opérations du système.

## Fonctionnalités

### Général

*   **Authentification des utilisateurs :** Connexion sécurisée pour les différents rôles d'utilisateurs ([`login.php`](login.php)).
*   **Inscription des utilisateurs :** Permet aux nouveaux utilisateurs (probablement les parents) de s'inscrire ([`inscription.html`](inscription.html), [`inscription.php`](inscription.php)).
*   **Intégration de la base de données :** Se connecte à une base de données MySQL pour la persistance des données ([`db.php`](db.php)).

### Module Parent

*   **Tableau de bord Parent :** Interface principale pour les parents ([`parent.php`](parent.php)).
*   **Recharge de compte :** Permet aux parents d'ajouter des fonds/points au compte d'un enfant ([`recharger.php`](recharger.php)).
*   **Gestion des comptes enfants :** (Implicite) Interaction avec les comptes enfants, éventuellement via [`ajouter.php`](ajouter.php) pour ajouter de nouveaux enfants ou gérer ceux existants.
*   **Historique des transactions :** Consulter les détails des transactions ([`transaction.php`](transaction.php)).

### Module Enfant

*   **Tableau de bord Enfant :** Interface principale pour les enfants ([`espace_enfant.php`](espace_enfant.php), [`enfant/home2.php`](enfant/home2.php)).
*   **Navigation des articles :** Voir les articles disponibles ([`enfant/articles.php`](enfant/articles.php), [`enfant/get_articles.php`](enfant/get_articles.php)).
*   **Panier d'achat :** Ajouter des articles à un panier d'achat ([`enfant/ajouter_panier.php`](enfant/ajouter_panier.php), [`enfant/panier.php`](enfant/panier.php)).
*   **Gestion du panier :** Modifier les quantités ou supprimer des articles du panier ([`enfant/modifier_quantite.php`](enfant/modifier_quantite.php), [`enfant/supprimer_article.php`](enfant/supprimer_article.php)).
*   **Passer commande :** Valider et passer les commandes ([`enfant/valider_commande.php`](enfant/valider_commande.php)).
*   **Historique des commandes :** Consulter les commandes passées ([`enfant/mes_commandes.php`](enfant/mes_commandes.php)).
*   **Notifications :** Marquer les notifications comme lues ([`enfant/marquer_notifie.php`](enfant/marquer_notifie.php)).
*   **Vérification de commande :** Vérifier le statut ou les détails de la commande ([`enfant/verifier_commande.php`](enfant/verifier_commande.php)).

### Module Partenaire

*   **Tableau de bord Partenaire :** Interface principale pour les partenaires/vendeurs ([`partenaire/home_p.php`](partenaire/home_p.php)).
*   **Gestion des commandes :** Consulter et gérer les commandes passées par les enfants ([`partenaire/gerer_commandes.php`](partenaire/gerer_commandes.php), [`partenaire/get_commandes.php`](partenaire/get_commandes.php)).

### Module Admin

*   **Tableau de bord Admin :** Panneau d'administration central pour le système ([`admin/acceuil.php`](admin/acceuil.php)). (D'autres fonctionnalités seraient implémentées ici).

## Technologies Utilisées

*   **Backend :** PHP
*   **Frontend :** HTML, CSS ([`home.css`](home.css))
*   **Base de données :** MySQL (via `db.php`)
*   **Gestion des dépendances :** Composer ([`composer.json`](composer.json))

## Structure du Projet

*   **`/` (Racine) :** Fichiers d'application principaux, y compris l'authentification, l'inscription, la connexion à la base de données et les points d'entrée principaux pour les parents/enfants.
*   **`admin/` :** FichiersH liés à l'interface de l'administrateur.
*   **`enfant/` :** Fichiers spécifiquement pour l'interface de l'enfant, y compris les achats, le panier et la gestion des commandes.
*   **`partenaire/` :** Fichiers pour l'interface partenaire/vendeur pour gérer les commandes.
*   **`vendor/` :** Dépendances PHP gérées par Composer.
*   **`images/` (dans `admin/` et `enfant/`) :** Espace réservé pour les images spécifiques à ces sections.

## Instructions de Configuration

1.  **Cloner le dépôt :**

    ```bash
    git clone <repository-url>
    cd <project-directory>
    ```

2.  **Configuration de la base de données :**

    *   Créer une base de données MySQL (par exemple, `enfant_system`).
    *   Mettre à jour les détails de connexion à la base de données dans [`db.php`](db.php) pour qu'ils correspondent à vos identifiants.
    *   Vous devrez créer les tables nécessaires (par exemple, `users`, `children`, `articles`, `panier`, `commandes`, `transactions`). Un exemple de schéma serait nécessaire.

3.  **Installer les dépendances PHP :**

    *   Assurez-vous que Composer est installé.
    *   Exécutez la commande suivante à la racine du projet pour installer les dépendances :

    ```bash
    composer install
    ```

4.  **Configuration du serveur web :**

    *   Déployer le projet sur un serveur web (par exemple, Apache, Nginx) avec support PHP.
    *   Assurez-vous que le document root du serveur web pointe vers le répertoire du projet.

5.  **Accéder à l'application :**

    *   Ouvrez votre navigateur web et naviguez vers l'URL configurée (par exemple, `http://localhost/`).
    *   Commencez par créer un compte via [`inscription.html`](inscription.html) ou connectez-vous via [`login.php`](login.php).

## Utilisation

*   **Parents :** S'inscrire, se connecter, gérer les comptes enfants et recharger leurs points.
*   **Enfants :** Se connecter, parcourir les articles, les ajouter au panier et passer des commandes.
*   **Partenaires :** Se connecter pour consulter et traiter les commandes passées par les enfants.
*   **Admin :** Accéder au panneau d'administration pour superviser l'ensemble du système, gérer les utilisateurs, les articles et les commandes.
