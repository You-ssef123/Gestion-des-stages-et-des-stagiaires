📌 Description du Projet
L'application web Gestion des Stagiaires est conçue pour faciliter la gestion des stagiaires au sein d'une entreprise ou d'un établissement de formation. Elle permet aux administrateurs et gestionnaires de suivre les candidatures, valider ou refuser des stagiaires, et organiser les stages efficacement.

⚙️ Technologies Utilisées
Frontend : HTML, CSS

Backend : PHP

Base de données : MySQL

👥 Rôles et Fonctionnalités
🔹 1. Gestionnaire
📌 Valider ou refuser les candidatures des stagiaires.

📌 Affecter un stagiaire à un stage.

📌 Consulter la liste des stagiaires validés.

🔹 2. Administrateur
📌 Gérer les comptes des gestionnaires.

📌 Ajouter un stagiaire directement à un stage.

📌 Voir le tableau de bord et les statistiques.

📌 Gérer les stages (ajout, modification, suppression).

🗃️ Base de Données (Modèle Simplifié)
Tables Principales :
stagiaires (id, nom, prenom, email, cv, lm, stage_id, status)

stages (id, titre, description, durée)

gestionnaires (id, nom, prenom, email, mot_de_passe)

admins (id, nom, prenom, email, mot_de_passe)

stagiaires_valides (id, nom, prenom, email, stage_id, durée, date_validation)

stagiaires_refuses (id, nom, prenom, email, date_refus)

