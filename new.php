<?php
session_start();
session_unset();
$plainPassword = 'adminpass'; // Replace with the actual password
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

echo $hashedPassword;


?>