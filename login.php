<?php
   include 'db.conf';
   include 'wrappers.php';

   cabecera("Logeado");

   session_start();
   $login = $_POST['username'];
   $password = $_POST['password'];

   mysql_connect(dbhost,dbuser,dbpass);
   mysql_select_db(dbname);
   $query = "SELECT * FROM ".dbname.".usuario WHERE username='$login'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);

   if(md5($password) == $row['password']){
      $_SESSION['username']=$row['username'];
      $_SESSION['logged']='true';
      echo "Correcto, estás logeado<BR><BR>";
      echo "Ir a mi <a href='personal.php?id=".$row['id']."'>página personal</a><BR>";
      echo "<a href='newpiece.php'>Insertar pieza</a><BR>";
   }else{
      echo "Usuario o contrasña incorrectos<BR>";
   }
   echo "<a href='search.php'>Buscar pieza</a><BR>";
   echo "<BR><a href='./'>Volver al inicio</a>";

   pie();
?>
