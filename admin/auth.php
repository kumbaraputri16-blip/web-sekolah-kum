<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
