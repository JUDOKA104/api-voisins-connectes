# Voisins Connectés - API REST (Back-end)

## 📝 Présentation du projet
Ce projet constitue le cœur métier (Back-end) de la plateforme web "Voisins Connectés", développée pour l'association Lien Urbain. L'objectif de l'application est de faciliter l'entraide de quartier via une plateforme moderne, robuste et sécurisée.

L'architecture repose sur une API REST stricte, totalement découplée de l'interface utilisateur.

## 🛠 Technologies Utilisées
* **Framework :** Symfony 8.0 (PHP 8.5+)
* **Base de données :** PostgreSQL
* **Sécurité :** LexikJWTAuthenticationBundle (Authentification par Token JWT)
* **CORS :** NelmioCorsBundle

## ⚙️ Fonctionnalités Principales (Cahier des charges)
* **Sécurité & Espace Utilisateur :** Inscription, authentification JWT, hachage des mots de passe.
* **Cœur de Métier :** API CRUD pour la gestion des annonces.
* **Administration :** Routes sécurisées pour la modération (suppression d'annonces, statistiques).

## 🚀 Fonctionnalités Exclusives & "Carte Blanche"
Pour garantir que notre solution soit la plus innovante et engageante possible, nous avons intégré des fonctionnalités majeures allant bien au-delà du périmètre initial :

1. **Système de Commentaires Avancé :** Implémentation d'un flux d'interactions complet (via `CommentaireController`) permettant aux utilisateurs d'échanger directement sous les annonces avant d'accepter un service.
2. **Gamification (Système de Badges) :** Ajout d'une logique métier récompensant l'engagement des utilisateurs (ex: nombre de services rendus) par l'attribution de badges.
3. **Le "Bot" Voisins Connectés :** Intégration d'un bot automatisé pour dynamiser la plateforme sur les annonces.
4. **Modération Poussée (Bannissement) :** Un système de bannissement strict avec révocation des accès et blocage des tokens pour protéger la communauté de Lien Urbain.
5. **Sécurité Anti-Spam / Brute Force :** Intégration du `Rate Limiter` pour protéger l'API contre les attaques sur les routes sensibles (login).

## 📦 Installation et Lancement

1. Cloner le dépôt.
2. Installer les dépendances : `composer install`
3. Configurer les variables d'environnement dans le fichier `.env`.
4. Générer les clés JWT : `php bin/console lexik:jwt:generate-keypair`
5. Créer la base de données et jouer les migrations :
   `php bin/console d:d:c` && `php bin/console d:m:m`
6. Lancer le serveur local : `symfony serve`
