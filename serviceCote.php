<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit(0);
}

require_once 'config/database.php';

// Définition des URL des services externes
define('INSCRIPTION_API_URL', 'http://localhost/serviceInscription/getStudent.php');
define('COURS_API_URL', 'http://localhost/serviceCours/getCours.php');

// Fonctions utilitaires
function getNomEtudiant($id) {
    $url = INSCRIPTION_API_URL . '?etudiant_id=' . urlencode($id);
    $response = @file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        return $data['nom'] ?? 'Inconnu';
    }
    return 'Inconnu';
}

function getNomCours($code) {
    $url = COURS_API_URL . '?code_cours=' . urlencode($code);
    $response = @file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        return $data['nom'] ?? 'Inconnu';
    }
    return 'Inconnu';
}

try {
    $database = new Database();
    $db = $database->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $etudiant_id = $_GET['etudiant_id'] ?? null;
        $code_cours = $_GET['code_cours'] ?? null;

        if ($etudiant_id && $code_cours) {
            $query = "SELECT * FROM cote WHERE etudiant_id = :etudiant_id AND code_cours = :code_cours";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':etudiant_id', $etudiant_id);
            $stmt->bindParam(':code_cours', $code_cours);
        } elseif ($etudiant_id) {
            $query = "SELECT * FROM cote WHERE etudiant_id = :etudiant_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':etudiant_id', $etudiant_id);
        } elseif ($code_cours) {
            $query = "SELECT * FROM cote WHERE code_cours = :code_cours";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':code_cours', $code_cours);
        } else {
            $query = "SELECT * FROM cote";
            $stmt = $db->prepare($query);
        }

        $stmt->execute();
        $cotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ajouter les noms à chaque côte
        foreach ($cotes as &$cote) {
            $cote['nom_etudiant'] = getNomEtudiant($cote['etudiant_id']);
            $cote['nom_cours'] = getNomCours($cote['code_cours']);
        }

        // Réponse avec la structure souhaitée
        http_response_code(200);
        echo json_encode(["cotes" => $cotes], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (is_array($data)) {
            foreach ($data as $item) {
                if (
                    isset($item['etudiant_id']) &&
                    isset($item['code_cours']) &&
                    isset($item['valeur']) &&
                    is_numeric($item['valeur'])
                ) {
                    $query = "INSERT INTO cote (etudiant_id, code_cours, valeur) VALUES (:etudiant_id, :code_cours, :valeur)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':etudiant_id', $item['etudiant_id']);
                    $stmt->bindParam(':code_cours', $item['code_cours']);
                    $stmt->bindParam(':valeur', $item['valeur']);

                    if (!$stmt->execute()) {
                        http_response_code(500);
                        echo json_encode(["success" => false, "message" => "Erreur lors de l'enregistrement pour un élément"]);
                        exit;
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "Champs requis manquants ou invalides dans un élément"]);
                    exit;
                }
            }

            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Côtes enregistrées avec succès"]);
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Le format des données envoyées est incorrect (doit être un tableau)"]);
        }
    }

    else {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur interne",
        "error" => $e->getMessage()
    ]);
}
