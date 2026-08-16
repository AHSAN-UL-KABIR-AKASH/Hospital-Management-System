<?php
/**
 * Serves a medical document ONLY to authenticated admins.
 * Documents are stored outside any publicly-guessable static path assumption;
 * this script is the sole authorized access point.
 */
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$docId = isset($_GET['doc']) ? (int) $_GET['doc'] : 0;
if (!$docId) {
    http_response_code(404);
    die('Document not found.');
}

$stmt = getDB()->prepare('SELECT * FROM medical_documents WHERE id = ?');
$stmt->execute([$docId]);
$doc = $stmt->fetch();

if (!$doc) {
    http_response_code(404);
    die('Document not found.');
}

$path = UPLOAD_DOCUMENTS_DIR . $doc['document_path'];
$realBase = realpath(UPLOAD_DOCUMENTS_DIR);
$realPath = realpath($path);

// Prevent path traversal - ensure resolved file is inside the documents directory
if (!$realPath || !$realBase || strpos($realPath, $realBase) !== 0 || !is_file($realPath)) {
    http_response_code(404);
    die('Document not found.');
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $realPath);
finfo_close($finfo);

header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . basename($doc['document_name']) . '"');
header('X-Content-Type-Options: nosniff');
readfile($realPath);
exit;
