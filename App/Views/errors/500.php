<?php
$status = 500;
$title = "Internal Server Error";
$description = $errorMessage ?? "An unexpected server error occurred while processing your request.";
require __DIR__ . '/error.php';
