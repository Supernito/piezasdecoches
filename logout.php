<?php

   include 'wrappers.php';

   cabecera("Logout");

   session_destroy();
   echo "Has salido correctamente! <BR><BR>";
   echo "<a href='search.php'>Buscar pieza</a><BR>";
   echo "<BR><a href='./'>Volver al inicio</a>";

   pie();
?>
