<?php
require_once __DIR__ . '/../inc/helpers.php';
$_SESSION = [];
session_destroy();
header('Location: ' . SITE_URL . '/admin/index.php');
