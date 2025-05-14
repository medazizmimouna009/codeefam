"""# 🌐 CodeFam – Plateforme Éducative de Codage (Web)

<p align="center">
  <a href="https://github.com/CodeFam">
    <img src="https://img.shields.io/badge/Join%20us%20on-GitHub-blue" alt="CodeFam GitHub"/>
  </a>
  <a href="https://twitter.com/CodeFamApp">
    <img src="https://img.shields.io/badge/Follow%20us%20on-Twitter-1DA1F2" alt="CodeFam Twitter"/>
  </a>
</p>

## 📖 Description du Projet

**CodeFam (Coding Family)** est une application web développée avec le framework **Symfony PHP** dans le cadre d'un projet éducatif. Il s'agit d'une plateforme d'e-learning dédiée à l'apprentissage du codage, inspirée de Coursera ou Udemy. Elle permet aux utilisateurs de :

- Accéder à une bibliothèque de cours structurés, organisés en catégories avec vidéos et PDF.
- Participer à des quiz interactifs pour évaluer leurs compétences.
- Interagir via un forum intégré avec posts et commentaires.
- Acheter des offres premium via un système de paiement sécurisé (**Stripe**).
- Gérer leur profil et suivre leur progression.
- Bénéficier d'une recherche intelligente des offres par prix.

L'objectif est de fournir une expérience d'apprentissage intuitive, sécurisée et engageante pour les passionnés de programmation.

## 🗂 Table des Matières

- [Pré-requis](#pré-requis)
- [Installation](#installation)
- [Utilisation](#utilisation)
- [Fonctionnalités](#fonctionnalités)
- [APIs Utilisées](#apis-utilisées)
- [Contribuer](#contribuer)
- [Démo](#démo)
- [Contact](#contact)

## ✅ Pré-requis

Avant d’installer le projet, assurez-vous d’avoir les éléments suivants installés sur votre machine :

- **PHP 8.1.26** ou supérieur
- **[Composer](https://getcomposer.org/)** : Gestion des dépendances PHP
- **[Symfony CLI](https://symfony.com/download)** : Pour lancer le serveur
- **MySQL** : Base de données
- **[Git](https://git-scm.com/)** : Pour cloner le repository
- **Node.js et Yarn** : Pour gérer les assets frontend
- **wkhtmltopdf** : Pour la génération de PDF avec `knplabs/knp-snappy-bundle`
- **Clé API [Stripe](https://stripe.com/)** : Pour les paiements (mode test recommandé)
- **Clé API [Google Cloud](https://cloud.google.com/)** : Pour l'authentification Google
- **Service de messagerie (ex. Mailgun)** : Pour l'envoi d'emails via `symfony/mailgun-mailer`

### Dépendances Symfony recommandées :
- `knplabs/knp-snappy-bundle` : Génération de PDF (quiz résultats)
- `symfony/mailer` et `symfony/mailgun-mailer` : Gestion des emails
- `knplabs/knp-paginator-bundle` : Pagination des listes
- `claroline/video-player-bundle` : Gestion des vidéos
- `highcharts/highcharts` : Visualisation des graphiques (via Yarn)

## ⚙ Installation

1. **Cloner le repository** :
   ```bash
   git clone https://github.com/CodeFam/codefamWeb.git
   cd codefamWeb
   ```

2. **Installer les dépendances PHP** :
   ```bash
   composer install
   ```

3. **Installer les dépendances frontend** :
   ```bash
   yarn install
   ```

4. **Build des assets frontend (Tailwind CSS, Highcharts, etc.)** :
   ```bash
   yarn build
   ```

5. **Configurer la base de données** :
   - Créez une base de données MySQL :
     ```sql
     CREATE DATABASE codefam_db;
     ```
   - Configurez les informations de connexion dans `.env` ou `.env.local` :
     ```env
     DATABASE_URL="mysql://votre_utilisateur:votre_mot_de_passe@127.0.0.1:3306/codefam_db"
     STRIPE_API_KEY=sk_test_votre_cle_stripe
     GOOGLE_API_KEY=votre_cle_google
     MAILER_DSN=mailgun+smtp://votre_clé_mailgun
     ```
   - Exécutez les migrations pour créer les tables :
     ```bash
     symfony console doctrine:database:create
     symfony console doctrine:migrations:migrate
     ```

6. **Installer wkhtmltopdf (pour PDF)** :
   - Suivez les instructions sur [wkhtmltopdf.org](https://wkhtmltopdf.org/downloads.html) pour votre système.

7. **Lancer le serveur local Symfony** :
   ```bash
   symfony server:start
   ```

8. **Accéder à l'application** :
   👉 [http://127.0.0.1:8000](http://127.0.0.1:8000)

## 🚀 Utilisation

Une fois l’application lancée, vous pouvez :

- **Explorer les cours** : Parcourez les catégories et accédez aux vidéos/PDF.
- **Répondre à des quiz** : Testez vos connaissances avec des quiz personnalisés, visualisez les statistiques, et téléchargez les résultats en PDF.
- **Interagir dans le forum** : Créez des posts et commentez.
- **Acheter des offres** : Utilisez une carte de test Stripe (ex. `4242 4242 4242 4242`) pour simuler un achat.
- **Gérer votre profil** : Connectez-vous via email/Google, réinitialisez votre mot de passe, et suivez votre progression.
- **Rechercher des offres** : Filtrez les offres par prix pour trouver celles qui correspondent à votre budget.
- **Administrer (Admin/Tuteur)** : Gérez les utilisateurs, cours, quiz, et visualisez les statistiques.

## 🛠 Fonctionnalités

### Gestion des Utilisateurs
Pour une application web développée avec le framework Symfony PHP, un système de gestion des utilisateurs performant et sécurisé a été mis en place. Ce système permet :
- Une authentification fluide via email et mot de passe ou via un compte Google, avec une interface intuitive pour les utilisateurs.
- Une fonctionnalité de réinitialisation de mot de passe oublié, garantissant une récupération de compte simple et sécurisée, utilisant `symfony/mailer` et `symfony/mailgun-mailer` pour l'envoi d'emails.
- Un système CRUD complet pour les administrateurs, permettant la création, lecture, modification et suppression des comptes, offrant un contrôle total sur les utilisateurs.
- Un tableau de bord statistique fournissant des visualisations claires des données relatives aux utilisateurs et à leurs interactions, renforçant la gestion et le suivi au sein de l'application.

### Gestion des Offres et Achats
- Opérations CRUD pour les offres et achats (création, modification, suppression, consultation).
- Paiement en ligne sécurisé via **Stripe**.
- Interface détaillée des achats (date, montant, offre, utilisateur), paginée avec `knplabs/knp-paginator-bundle`.
- Recherche intelligente des offres par prix pour filtrer selon le budget.

### Rôles et Permissions
- **Administrateur/Tuteur** :
  - Gestion complète (CRUD) des catégories, noms de cours, cours, et vidéos (gérées via `claroline/video-player-bundle`).
  - Recherche dynamique sur toutes les entités.
  - Visualisation des moyennes des évaluations par vidéo.
- **Utilisateur** :
  - Accès aux catégories abonnées.
  - Visualisation/téléchargement des cours (vidéos, PDF).
  - Évaluation des vidéos.
  - Progression : +1% pour un PDF téléchargé, +90% pour les vidéos terminées.

### Forum (Posts et Commentaires)
- Création, modification, suppression de posts avec support d'images.
- Système de commentaires hiérarchique (réponses imbriquées).
- Filtrage de contenu (profanité, XSS).
- Recherche de posts, paginée avec `knplabs/knp-paginator-bundle`.
- Entités :
  ```php
  Post {
      id: int
      userId: int
      contenu: string
      dateCreation: DateTime
      image: string (optional)
  }
  Commentaire {
      id: int
      userId: int
      postId: int
      parentId: int (nullable)
      contenu: string
      dateCreation: DateTime
  }
  ```

### Gestion des Quiz
- Création, modification, suppression de quiz (noms, cours, niveaux : facile, moyen, difficile).
- Gestion des questions avec réponses à choix multiples.
- Génération aléatoire de quiz basée sur le cours, le niveau de difficulté et le nombre de questions.
- Administration des réponses (correctes/incorrectes).
- Visualisation des résultats via graphiques (PieChart) avec statistiques détaillées, rendus avec `highcharts/highcharts`.
- Téléchargement des résultats de quiz au format PDF, généré avec `knplabs/knp-snappy-bundle`.
- Recherche dynamique des quiz par nom, cours, ou niveau, paginée avec `knplabs/knp-paginator-bundle`.

### Sécurité
- Protection contre les captures d’écran et enregistrements vidéo.
- Contrôle d’accès basé sur les rôles (admin, tuteur, utilisateur).
- Validation des entrées (longueur, profanité, XSS).
- Authentification et permissions sécurisées.

## 🌐 APIs Utilisées

- **[Stripe](https://stripe.com/)** : Paiements sécurisés.
- **[Google Cloud API](https://cloud.google.com/)** : Authentification Google.

## 🤝 Contribuer

1. Forkez le projet.
2. Créez une branche : `git checkout -b feature/nouvelle-fonctionnalite`.
3. Commitez vos changements : `git commit -m "Ajout de nouvelle fonctionnalité"`.
4. Poussez votre branche : `git push origin feature/nouvelle-fonctionnalite`.
5. Ouvrez une Pull Request.

### Bonnes pratiques :
- Suivez les conventions PSR pour PHP.
- Validez les entrées et utilisez des requêtes préparées pour la base de données.
- Testez avec `phpunit` avant de soumettre.

## 🎥 Démo

👉 [Regardez la démo de CodeFam sur YouTube](https://www.youtube.com/watch?v=exemple) *(Lien à mettre à jour après publication)*

## 📬 Contact

- **GitHub** : [@CodeFam](https://github.com/CodeFam)
- **Email** : support@codefam.com
- **Twitter** : [@CodeFamApp](https://twitter.com/CodeFamApp)

---

<p align="center">
  <img src="https://img.shields.io/badge/Made%20with-Symfony-blue" alt="Made with Symfony"/>
  <img src="https://img.shields.io/badge/License-MIT-green" alt="License MIT"/>
</p>
"""
