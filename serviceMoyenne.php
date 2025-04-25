<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

define('COTE_API_URL', 'http://localhost/serviceCote/serviceCote.php');
define('INSCRIPTION_API_URL', 'http://localhost/serviceInscription/getStudent.php');
define('COURS_API_URL', 'http://localhost/serviceCours/getCours.php');

// Récupère toutes les côtes
function getCotes($params = []) {
    $url = COTE_API_URL . '?' . http_build_query($params);
    $json = file_get_contents($url);
    return json_decode($json, true)['cotes'] ?? [];
}

// Récupère le nom d’un étudiant
function getEtudiantName($etudiant_id) {
    $url = INSCRIPTION_API_URL . '?etudiant_id=' . $etudiant_id;
    $json = @file_get_contents($url);
    $data = json_decode($json, true);
    return $data['nom'] ?? 'Nom non trouvé';
}

// Récupère le nom d’un cours
function getCoursName($code_cours) {
    $url = COURS_API_URL . '?code_cours=' . $code_cours;
    $json = @file_get_contents($url);
    $data = json_decode($json, true);
    return $data['nom'] ?? 'Nom de cours non trouvé';
}

// Paramètres GET
$etudiant_id = $_GET['etudiant_id'] ?? null;
$code_cours = $_GET['code_cours'] ?? null;

// Cas 1 : Tous les étudiants et tous les cours
if (!$etudiant_id && !$code_cours) {
    $cotes = getCotes();

    if (empty($cotes)) {
        echo json_encode(["success" => false, "message" => "Aucune côte disponible."]);
        exit;
    }

    // Moyennes par étudiant et cours
    $grouped = [];
    foreach ($cotes as $cote) {
        $key = $cote['etudiant_id'] . '-' . $cote['code_cours'];
        $grouped[$key]['etudiant_id'] = $cote['etudiant_id'];
        $grouped[$key]['code_cours'] = $cote['code_cours'];
        $grouped[$key]['notes'][] = $cote['valeur'];
    }

    $result = [];
    foreach ($grouped as $entry) {
        $result[] = [
            'etudiant_id' => $entry['etudiant_id'],
            'nom' => getEtudiantName($entry['etudiant_id']),
            'code_cours' => $entry['code_cours'],
            'cours' => getCoursName($entry['code_cours']),
            'moyenne' => round(array_sum($entry['notes']) / count($entry['notes']), 2)
        ];
    }

    echo json_encode(["success" => true, "moyennes" => $result]);
    exit;
}

// Cas 2 : Moyennes par cours
if ($code_cours && !$etudiant_id) {
    $cotes = getCotes(['code_cours' => $code_cours]);

    if (empty($cotes)) {
        echo json_encode(["success" => false, "message" => "Aucune côte pour ce cours."]);
        exit;
    }

    $grouped = [];
    foreach ($cotes as $cote) {
        $grouped[$cote['etudiant_id']][] = $cote['valeur'];
    }

    $result = [];
    foreach ($grouped as $etudiant_id => $notes) {
        $result[] = [ 
            'etudiant_id' => $etudiant_id,
            'nom' => getEtudiantName($etudiant_id),
            'cours' => getCoursName($code_cours),
            'moyenne' => round(array_sum($notes) / count($notes), 2)
        ];
    }

    echo json_encode(["success" => true, "code_cours" => $code_cours, "moyennes" => $result]);
    exit;
}

// Cas 3 : Moyenne d’un étudiant dans un cours
if ($etudiant_id && $code_cours) {
    $cotes = getCotes(['etudiant_id' => $etudiant_id, 'code_cours' => $code_cours]);
    $notes = array_column($cotes, 'valeur');

    if (empty($notes)) {
        echo json_encode(["success" => false, "message" => "Pas de côte pour cet étudiant et ce cours."]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "etudiant_id" => $etudiant_id,
        "nom" => getEtudiantName($etudiant_id),
        "code_cours" => $code_cours,
        "cours" => getCoursName($code_cours),
        "moyenne" => round(array_sum($notes) / count($notes), 2)
    ]);
    exit;
}

// Cas 4 : Moyennes par cours pour un étudiant
if ($etudiant_id && !$code_cours) {
    $cotes = getCotes(['etudiant_id' => $etudiant_id]);

    if (empty($cotes)) {
        echo json_encode(["success" => false, "message" => "Pas de côte pour cet étudiant."]);
        exit;
    }

    $grouped = [];
    foreach ($cotes as $cote) {
        $grouped[$cote['code_cours']][] = $cote['valeur'];
    }

    $result = [];
    foreach ($grouped as $code_cours => $notes) {
        $result[] = [
            'code_cours' => $code_cours,
            'cours' => getCoursName($code_cours),
            'moyenne' => round(array_sum($notes) / count($notes), 2)
        ];
    }

    echo json_encode([
        "success" => true,
        "etudiant_id" => $etudiant_id,
        "nom" => getEtudiantName($etudiant_id),
        "moyennes" => $result
    ]);
    exit;
}

http_response_code(400);
echo json_encode(["success" => false, "message" => "Paramètres manquants ou invalides."]);
