# Service Moyenne

`serviceMoyenne.php` est un service PHP qui calcule les moyennes des notes des étudiants pour différents cours. Il interagit avec d'autres services pour récupérer les informations nécessaires, comme les noms des étudiants et des cours.

## Fonctionnalités

Le service prend en charge plusieurs cas d'utilisation en fonction des paramètres fournis dans la requête GET :

1. **Tous les étudiants et tous les cours**  
   - Retourne les moyennes des notes pour chaque étudiant et chaque cours.
   - URL : `.../serviceMoyenne.php`
   - Paramètres : Aucun.

2. **Moyennes par cours**  
   - Retourne les moyennes des notes pour un cours spécifique.
   - URL : `/serviceMoyenne.php`
   - Paramètres : `code_cours`.

3. **Moyenne d’un étudiant dans un cours**  
   - Retourne la moyenne des notes pour un étudiant spécifique dans un cours spécifique.
   - URL : `/serviceMoyenne.php`
   - Paramètres : `etudiant_id`, `code_cours`.

4. **Moyennes par cours pour un étudiant**  
   - Retourne les moyennes des notes pour tous les cours d’un étudiant spécifique.
   - URL : `/serviceMoyenne.php`
   - Paramètres : `etudiant_id`.

## Dépendances

Le service dépend des services externes suivants :
- **Service Cote** : Utilisé pour récupérer les notes des étudiants.
- **Service Inscription** : Utilisé pour récupérer les noms des étudiants.
- **Service Cours** : Utilisé pour récupérer les noms des cours.

## Structure des Réponses

Les réponses sont au format JSON et incluent les informations suivantes :

### Cas 1 : Tous les étudiants et tous les cours
```json
{
  "success": true,
  "moyennes": [
    {
      "etudiant_id": "123",
      "nom": "John Doe",
      "code_cours": "MATH101",
      "cours": "Mathématiques",
      "moyenne": 15.5
    }
  ]
}

### Cas 2 : Moyennes par cours

{
  "success": true,
  "code_cours": "MATH101",
  "moyennes": [
    {
      "etudiant_id": "123",
      "nom": "John Doe",
      "cours": "Mathématiques",
      "moyenne": 15.5
    }
  ]
}

### Cas 3 : Moyenne d’un étudiant dans un cours

{
  "success": true,
  "etudiant_id": "123",
  "nom": "John Doe",
  "code_cours": "MATH101",
  "cours": "Mathématiques",
  "moyenne": 15.5
}

### Cas 4 : Moyennes par cours pour un étudiant

{
  "success": true,
  "etudiant_id": "123",
  "nom": "John Doe",
  "moyennes": [
    {
      "code_cours": "MATH101",
      "cours": "Mathématiques",
      "moyenne": 15.5
    }
  ]
}

En cas d'erreur, le service retourne une réponse JSON avec un message d'erreur et un code HTTP approprié. Par exemple :

{
  "success": false,
  "message": "Aucune côte disponible."
}
