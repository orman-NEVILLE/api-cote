# Service Cote

Le `serviceCote' est un service PHP qui permet de gérer les côtes (notes) des étudiants pour différents cours. Il prend en charge les opérations de récupération et d'ajout de côtes via des requêtes HTTP.

## Fonctionnalités

### 1. Récupération des côtes (GET)
Le service permet de récupérer les côtes en fonction des paramètres fournis :
- **Toutes les côtes** : Si aucun paramètre n'est fourni.
- **Côtes pour un étudiant spécifique** : Si `etudiant_id` est fourni.
- **Côtes pour un cours spécifique** : Si `code_cours` est fourni.
- **Côtes pour un étudiant dans un cours spécifique** : Si `etudiant_id` et `code_cours` sont fournis.

#### Exemple de réponse
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


### 2. Ajout de côtes (POST)

Le service permet d'ajouter plusieurs côtes en une seule requête. Les données doivent être envoyées au format JSON avec un tableau d'objets contenant les champs suivants :

etudiant_id : Identifiant de l'étudiant.
code_cours : Code du cours.
valeur : Valeur de la côte (doit être un nombre).

[
  {
    "etudiant_id": "123",
    "code_cours": "MATH101",
    "valeur": 15
  },
  {
    "etudiant_id": "124",
    "code_cours": "PHYS101",
    "valeur": 18
  }
]

Exemple de données envoyées

{
  "success": true,
  "message": "Côtes enregistrées avec succès"
}

Exemple de réponse en cas d'erreur

{
  "success": false,
  "message": "Erreur lors de l'enregistrement pour un élément"
}

Dépendances

Le service dépend des services externes suivants pour enrichir les données :

Service Inscription. : Utilisé pour récupérer les noms des étudiants.
Service Cours : Utilisé pour récupérer les noms des cours.
