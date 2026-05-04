<?php
// apply.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'config/database.php';
    $firstName = $_POST['first_name'];
    // ... collect fields, generate an application ID, insert into a new `applicants` table
    // Similar to student registration, but status defaults to 'pending'
    $message = "Application submitted. Your reference: APP-...";
}
?>
<!DOCTYPE html>
<html><head>…</head><body>
<form method="post"> ... </form>
</body></html>