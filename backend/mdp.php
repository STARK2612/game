<?php
$mot_de_passe = 'revolver';
$hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
echo $hashed_password;
?>
