<?php
$mot_de_passe = '123456789';
$hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
echo $hashed_password;
?>
