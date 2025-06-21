<?php
// diagnostico_blob.php
require_once 'includes/app.php';

use Controllers\DotacionInventarioController;

echo "<h2>🔍 DIAGNÓSTICO CAMPOS BLOB</h2>";
echo "<pre>";

// Test diagnóstico
DotacionInventarioController::diagnosticarCamposBlobAPI();

echo "</pre>";
?>