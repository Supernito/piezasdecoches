<?php
   include 'db.conf';
   header('Content-Type: image/jpeg');
   mysql_connect(dbhost,dbuser,dbpass); 
   mysql_select_db(dbname);
   $id= $_GET['id'];
   $result= mysql_query("SELECT imagen FROM ".dbname.".pieza WHERE id='$id'");
   $row= mysql_fetch_array($result);
   $img= $row['imagen'];
   echo $img;
?>
