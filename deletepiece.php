<?php
 
   include 'db.conf';
   include 'wrappers.php';

   cabecera("Eliminar pieza");

   session_start();

   mysql_connect(dbhost,dbuser,dbpass);
   mysql_select_db(dbname);
   $id= $_GET['idpieza'];
   $loggedu = $_SESSION['username'];
   $query = "SELECT id, is_admin FROM ".dbname.".usuario WHERE username='$loggedu'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);
   if ($loggedu==$propietario || $row['is_admin']=='true'){
      mysql_query("DELETE FROM ".dbname.".pieza WHERE id=$id");
      echo "Pieza eliminada.<BR><BR>";
      echo "Ir a mi <a href='personal.php?id=".$row['id']."'>página personal</a><BR>";
      echo "<a href='newpiece.php'>Insertar pieza</a><BR>";
   }else{
      echo "No hagas eso, ¬¬<BR>";
   }
   echo "<a href='search.php'>Buscar pieza</a><BR>";
   echo "<BR><a href='./'>Volver al inicio</a>";

   pie();
?>
