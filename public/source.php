<?php
$basePath = realpath(__DIR__ . '/../storage/app/public');
$folder = isset($_GET['folder']) ? basename($_GET['folder']) : null;
$file = isset($_GET['file']) ? basename($_GET['file']) : null;

if($folder == 'category-banner') $folder = 'category/banner';

$imagePath = realpath("$basePath/$folder/$file");

if (!$imagePath || strpos($imagePath, $basePath) !== 0 || !file_exists($imagePath)) {
    http_response_code(404);
    echo 'Gambar tidak ada';
    exit();
}

$mimeType = mime_content_type($imagePath);
header('Access-Control-Allow-Origin: *');
header("Content-Type: $mimeType");
header('Content-Length: ' . filesize($imagePath));
readfile($imagePath);
exit();
