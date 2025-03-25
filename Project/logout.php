<?php
// Start the session
session_start();

// Destroy the session
session_unset();
session_destroy();

// Prevent the browser from caching the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to the home page
header("Location: ../home.php");
exit();
?>
