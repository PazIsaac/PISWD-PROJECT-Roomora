<?php
require_once __DIR__ . '/includes/sesion.php';
logout();
header('Location: index.php');
exit;
