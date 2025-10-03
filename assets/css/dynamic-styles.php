<?php
header('Content-Type: text/css');
require_once __DIR__ . '/../../includes/app_settings.php';

$settings = new AppSettings();
echo $settings->generateCSS();
?>
