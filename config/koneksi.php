<?php

$db_host = 'localhost';
$db_name = 'db_sekolah';
$db_user = 'root';
$db_pass = '';

try {
    $koneksi = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die(
        'Koneksi database gagal: ' .
        htmlspecialchars($e->getMessage())
    );
}

/*
 * Base URL website
 */
$folderProyek = dirname(__DIR__);
$docRoot = rtrim(
    str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''),
    '/'
);

$folderProyek = str_replace('\\', '/', $folderProyek);

$baseUrl = $docRoot !== ''
    ? str_replace($docRoot, '', $folderProyek)
    : '';

define('BASE_URL', $baseUrl === '' ? '' : $baseUrl);