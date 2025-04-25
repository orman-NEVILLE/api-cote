# Service Cote

Ce projet est un service PHP permettant de gérer les côtes (notes) des étudiants pour différents cours. Il interagit avec des services externes pour enrichir les données avec les noms des étudiants et des cours.

## Fonctionnalités

Le service prend en charge les cas suivants :

1. **Récupération de toutes les côtes**  
   - Retourne toutes les côtes enregistrées dans la base de données.
   - **URL** : `https://api-service-cote.onrender.com/serviceCote.php`
   - **Paramètres** : Aucun.

2. **Récupération des côtes pour un étudiant spécifique**  
   - Retourne les côtes d'un étudiant donné.
   - **URL** : `https://api-service-cote.onrender.com/serviceCote.php`
   - **Paramètres** : `etudiant_id`.

3. **Récupération des côtes pour un cours spécifique**  
   - Retourne les côtes d'un cours donné.
   - **URL** : `https://api-service-cote.onrender.com/serviceCote.php`
   - **Paramètres** : `code_cours`.

4. **Récupération des côtes pour un étudiant dans un cours spécifique**  
   - Retourne les côtes d'un étudiant pour un cours donné.
   - **URL** : `https://api-service-cote.onrender.com/serviceCote.php`
   - **Paramètres** : `etudiant_id`, `code_cours`.

5. **Ajout de nouvelles côtes**  
   - Permet d'ajouter plusieurs côtes en une seule requête.
   - **URL** : `https://api-service-cote.onrender.com/serviceCote.php`
   - **Méthode** : POST
   - **Données** : Tableau JSON contenant les champs `etudiant_id`, `code_cours`, et `valeur`.

## Structure des Réponses

Les réponses sont au format JSON. Exemple pour une requête réussie :

### Récupération des côtes
```json
{
  "cotes": [
    {
      "etudiant_id": "123",
      "code_cours": "MATH101",
      "valeur": 15,
      "nom_etudiant": "John Doe",
      "nom_cours": "Mathématiques"
    }
  ]
}

## Ajout de côtes
{
  "success": true,
  "message": "Côtes enregistrées avec succès"
}

## En cas d'erreur
{
  "success": false,
  "message": "Erreur lors de l'enregistrement pour un élément"
}

## Dépendances
Le service utilise deux services externes pour enrichir les données :

Service Inscription : Défini par https://api-service-inscription.onrender.com/getStudent.php, utilisé pour récupérer les noms des étudiants.
Service Cours : Défini par [COURS_API_URL](https://api-cours.onrender.com/getCours.php), utilisé pour récupérer les noms des cours.
