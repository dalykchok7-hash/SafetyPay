# School/College E-commerce Platform for Children

This project is a web-based platform designed to enable children to make purchases from companies affiliated with their school or college. The system allows parents to supervise their children's spending by checking transaction histories and consumption details. It facilitates interactions between children, their parents, and partner companies (vendors/service providers). An administrator interface oversees the entire system operations.

## Features

### General
*   **User Authentication:** Secure login for different user roles ([`login.php`](login.php)).
*   **User Registration:** Allows new users (likely parents) to register ([`inscription.html`](inscription.html), [`inscription.php`](inscription.php)).
*   **Database Integration:** Connects to a MySQL database for data persistence ([`db.php`](db.php)).

### Parent Module
*   **Parent Dashboard:** Main interface for parents ([`parent.php`](parent.php)).
*   **Account Recharge:** Allows parents to add funds/points to a child's account ([`recharger.php`](recharger.php)).
*   **Child Account Management:** (Implied) Interaction with child accounts, possibly through [`ajouter.php`](ajouter.php) for adding new children or managing existing ones.
*   **Transaction History:** View transaction details ([`transaction.php`](transaction.php)).

### Child Module
*   **Child Dashboard:** Main interface for children ([`espace_enfant.php`](espace_enfant.php), [`enfant/home2.php`](enfant/home2.php)).
*   **Article Browsing:** View available items/articles ([`enfant/articles.php`](enfant/articles.php), [`enfant/get_articles.php`](enfant/get_articles.php)).
*   **Shopping Cart:** Add items to a shopping cart ([`enfant/ajouter_panier.php`](enfant/ajouter_panier.php), [`enfant/panier.php`](enfant/panier.php)).
*   **Cart Management:** Modify quantities or remove items from the cart ([`enfant/modifier_quantite.php`](enfant/modifier_quantite.php), [`enfant/supprimer_article.php`](enfant/supprimer_article.php)).
*   **Order Placement:** Validate and place orders ([`enfant/valider_commande.php`](enfant/valider_commande.php)).
*   **Order History:** View past orders ([`enfant/mes_commandes.php`](enfant/mes_commandes.php)).
*   **Notifications:** Mark notifications as read ([`enfant/marquer_notifie.php`](enfant/marquer_notifie.php)).
*   **Order Verification:** Check order status or details ([`enfant/verifier_commande.php`](enfant/verifier_commande.php)).

### Partner Module
*   **Partner Dashboard:** Main interface for partners/vendors ([`partenaire/home_p.php`](partenaire/home_p.php)).
*   **Order Management:** View and manage orders placed by children ([`partenaire/gerer_commandes.php`](partenaire/gerer_commandes.php), [`partenaire/get_commandes.php`](partenaire/get_commandes.php)).

### Admin Module
*   **Admin Dashboard:** Central administration panel for the system ([`admin/acceuil.php`](admin/acceuil.php)). (Further functionalities would be implemented here).

## Technologies Used

*   **Backend:** PHP
*   **Frontend:** HTML, CSS ([`home.css`](home.css))
*   **Database:** MySQL (via `db.php`)
*   **Dependency Management:** Composer ([`composer.json`](composer.json))

## Project Structure

*   **`/` (Root):** Core application files, including authentication, registration, database connection, and main entry points for parents/children.
*   **`admin/`:** Files related to the administrator's interface.
*   **`enfant/`:** Files specifically for the child's interface, including shopping, cart, and order management.
*   **`partenaire/`:** Files for the partner/vendor interface to manage orders.
*   **`vendor/`:** Composer-managed PHP dependencies.
*   **`images/` (within `admin/` and `enfant/`):** Placeholder for images specific to those sections.

## Setup Instructions

1.  **Clone the Repository:**
    ```bash
    git clone <repository-url>
    cd <project-directory>
    ```

2.  **Database Setup:**
    *   Create a MySQL database (e.g., `enfant_system`).
    *   Update the database connection details in [`db.php`](db.php) to match your credentials.
    *   You will need to create the necessary tables (e.g., `users`, `children`, `articles`, `panier`, `commandes`, `transactions`). Example schema would be needed.

3.  **Install PHP Dependencies:**
    *   Ensure you have Composer installed.
    *   Run the following command in the project root to install dependencies:
        ```bash
        composer install
        ```

4.  **Web Server Configuration:**
    *   Deploy the project on a web server (e.g., Apache, Nginx) with PHP support.
    *   Ensure the web server points its document root to the project directory.

5.  **Access the Application:**
    *   Open your web browser and navigate to the configured URL (e.g., `http://localhost/`).
    *   Start by registering an account via [`inscription.html`](inscription.html) or logging in via [`login.php`](login.php).

## Usage

*   **Parents:** Register, log in, manage child accounts, and recharge their points.
*   **Children:** Log in, browse articles, add them to the cart, and place orders.
*   **Partners:** Log in to view and process orders placed by children.
*   **Admin:** Access the admin panel to oversee the entire system, manage users, articles, and orders.