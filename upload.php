<?php
require 'vendor/autoload.php'; // Assurez-vous d'installer Google API Client via Composer

use Google\Client;
use Google\Service\Drive;

// Configuration de l'API Google
$client = new Client();
$client->setClientId('YOUR_CLIENT_ID'); // Remplacez par votre Client ID
$client->setClientSecret('YOUR_CLIENT_SECRET'); // Remplacez par votre Client Secret
$client->setRedirectUri('YOUR_REDIRECT_URI'); // Remplacez par votre URL de redirection
$client->addScope(Google\Service\Drive::DRIVE_FILE);

// Authentification
session_start();
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token['access_token'];
    header('Location: ' . filter_var($client->getRedirectUri(), FILTER_SANITIZE_URL));
    exit;
}

if (!isset($_SESSION['access_token'])) {
    header('Location: ' . $client->createAuthUrl());
    exit;
}

$client->setAccessToken($_SESSION['access_token']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->markdown)) {
        $markdownContent = $data->markdown;

        // Convertir Markdown en Google Docs
        $driveService = new Drive($client);
        $fileMetadata = new Drive\DriveFile([
            'name' => 'Document créé depuis Markdown',
            'mimeType' => 'application/vnd.google-apps.document',
        ]);

        // Créer un document Google Docs
        $doc = $driveService->files->create($fileMetadata, [
            'fields' => 'id'
        ]);

        $documentId = $doc->id;

        // Ajouter le contenu au document Google Docs
        $driveService->files->update($documentId, new Drive\DriveFile(), [
            'uploadType' => 'multipart',
            'data' => $markdownContent,
            'mimeType' => 'text/plain',
        ]);

        $link = "https://docs.google.com/document/d/$documentId/edit";
        echo json_encode(['link' => $link]);
    } else {
        echo json_encode(['error' => 'Aucun contenu Markdown fourni.']);
    }
} else {
    echo json_encode(['error' => 'Méthode non autorisée.']);
}
?>
