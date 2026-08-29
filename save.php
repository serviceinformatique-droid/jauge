<?php
header('Content-Type: application/json');

$dataDir = __DIR__ . '/data';

if(!is_dir($dataDir)){
    if(!@mkdir($dataDir, 0775, true)){
        http_response_code(500);
        echo json_encode(['error' => "Impossible de créer le dossier data/ dans " . __DIR__ . ". Vérifier que le serveur web peut écrire dans ce dossier."]);
        exit;
    }
}

if(!is_writable($dataDir)){
    http_response_code(500);
    echo json_encode(['error' => "Le dossier " . $dataDir . " n'est pas accessible en écriture par le serveur web. Essayer chmod 775 (ou 777) sur ce dossier."]);
    exit;
}

function safeKey($key){
    return preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$key);
}

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'GET'){
    $key = safeKey($_GET['key'] ?? '');
    if($key === ''){
        http_response_code(400);
        echo json_encode(['error' => 'Paramètre key manquant']);
        exit;
    }
    $file = $dataDir . '/' . $key . '.json';
    if(!file_exists($file)){
        http_response_code(404);
        echo json_encode(['error' => 'Clé introuvable']);
        exit;
    }
    echo file_get_contents($file);
    exit;
}

if($method === 'POST'){
    $input = json_decode(file_get_contents('php://input'), true);
    $key = safeKey($input['key'] ?? '');
    $value = $input['value'] ?? null;
    if($key === '' || $value === null){
        http_response_code(400);
        echo json_encode(['error' => 'Requête invalide']);
        exit;
    }
    $file = $dataDir . '/' . $key . '.json';
    $ok = @file_put_contents($file, json_encode(['value' => $value]));
    if($ok === false){
        $err = error_get_last();
        http_response_code(500);
        echo json_encode(['error' => "Impossible d'écrire " . $file . ". Détail PHP : " . ($err['message'] ?? 'inconnu')]);
        exit;
    }
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Méthode non supportée']);
