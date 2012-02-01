<?php
   include 'db.conf';
   include 'wrappers.php';

   mysql_connect(dbhost,dbuser,dbpass);
   mysql_select_db(dbname);
   session_start();

   $id = $_GET['id'];                  // Identificador de la pieza
   $query = "SELECT nombre, descripcion, precio, coche, imagen, propietario FROM ".dbname.".pieza WHERE id='$id'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);
   $propietario = $row['propietario'];   // Nombre del propietario de la pieza
   $nom_pieza   = $row['nombre'];        // Nombre de la pieza
   $precio      = $row['precio'];        // Precio de la pieza
   $coches      = $row['coche'];         // Coches a la que se aplica
   $propietario = $row['propietario'];   // Propietario de la pieza
   $descripcion = $row['descripcion'];   // Descripción de la pieza
   $imagen      = $row['imagen'];        // Imagen si la hay
   $visitante   = $_SESSION['username']; // Si esta logueado contiene el nombre del visitante

   $query = "SELECT is_admin,zona_h FROM ".dbname.".usuario WHERE username='$visitante'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);
   $visit_admin = $row['is_admin'];      // Si no estaba logueado este campo estará vacio
   if ($_SESSION['logged']=='true'){
      $zona_horaria = $row['zona_h'];    // Zona horaria del visitante si está logueado
   }else{
      $zona_horaria = "+00:00";          // Zona horaria del servidor si el visitante no está logueado
   }

   $query = "SELECT id,email FROM ".dbname.".usuario WHERE username='$propietario'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);
   $id_propietario = $row['id'];         // Id del propietario de la pieza.
   $correo = $row['email'];

   $query = "SELECT count(*) comen FROM ".dbname.".mensaje m WHERE m.pieza='$id'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);
   $comentarios = $row['comen'];         // Número de comentarios de la pieza
   
   cabecera("Pieza: \"$nom_pieza\"");
   if (!is_numeric($id) || $id <= 0){
      echo "No es una pieza válida";
      pie();die();
   }

// Scripts de confirmación
?>

   <script type="text/javascript">
      function confirmarmsg(){
         if(confirm('¿Estás seguro de que quieres borrar este mensaje?')) return true;
         else return false;
      }
   </script>

   <script type="text/javascript">
      function confirmarpz(){
         if(confirm('¿Estás seguro de que quieres borrar esta pieza?')) return true;
         else return false;
      }
   </script>

<?php

// Datos generales

   echo "<H4>$nom_pieza</H4>";
   if ($imagen) {
      //TODO Queda pendiente formatear correctamente la imagen segun su tamaño original
      $conf_img = "<a href='showimg.php?id=$id'> <img src='showimg.php?id=$id' alt='Imagen de la pieza'";
      // height=400 Width=600></a><BR><BR>";
      //$size = GetImageSize($imagen);
     echo $conf_img." height=160 width=130></a><BR><BR>";
   } else {
      echo "No hay imágen <BR>";
   }
   echo "Número de pieza: $id <BR>";
   echo "Coches en los que se aplica: $coches <BR>";
   echo "Precio: $precio <BR>";
   echo "Propietario: <a target='_blank' href='personal.php?id=$id_propietario'>$propietario</a> <BR>";
   echo "Correo: ";
   if ($_SESSION['logged']=='true'){
      echo "$correo<BR>";
   } else {
      echo "Logueate para ver el correo<BR>";
   }
   echo "<a href='piece.php?id=$id#Mensajes'>Comentarios:</a> $comentarios <BR>";
   echo "Descripción:<BR> $descripcion <BR>";

// Mensajes

   echo "<HR noshade><H4><span id='Mensajes'>Mensajes de la pieza \"$nom_pieza\":</span></H4>";

   // Formulario para insertar mensajes
   if ($_SESSION['logged']=='true'){
      echo "<form action='piece.php?id=$id#Mensajes' method='POST'> Tu comentario:
            <input class='textfield' name='comment' type='text' value=''  maxlength='255'/>&nbsp;
            <input type='submit' class='button' name='Introducir' value='Introducir'/>
            </form>";
   } else {
      echo "Logueate para poder escribir sobre esta pieza";
   }
   echo "<HR align='center' width='95%'>";

   // Se inserta el mensaje
   if ($_SESSION['logged']=='true' && isset($_POST['comment']) && trim($_POST['comment']) != "") {
      $comment = mysql_real_escape_string(trim(strip_tags(stripslashes($_POST['comment']))));
      if (strlen($comment > 255)) $comment = substr($comment,0,255);

      if (!empty($comment)) {
          $query = "INSERT INTO ".dbname.".mensaje (usuario,mensaje,dia,pieza) VALUES ('$visitante','$comment',NOW(),'$id')";
          $res = mysql_query($query) or die(mysql_error());
      }
   }

   // Aqui se borraran los mensajes si así­ lo ha pedido un administrador o el propietario
   if (($visit_admin=='true' || $visitante==$propietario) && isset($_GET['delete']) && is_numeric($_GET['delete'])) {
      $id_msg = (int)$_GET['delete'];
      $query = "DELETE FROM ".dbname.".mensaje WHERE id='$id_msg'";
      mysql_query($query) or die(mysql_error());
   }

   // Los mensajes en si
   $query = "SELECT *,CONVERT_TZ(dia, '+00:00', '$zona_horaria') as dia_local FROM ".dbname.".mensaje
      WHERE pieza='$id' ORDER BY dia DESC";
   $res = mysql_query($query) or die(mysql_error());
   while ($row = mysql_fetch_object($res)){
      echo "En ".$row->dia_local." el usuario ".$row->usuario." dijo: ".$row->mensaje;
      if ($visit_admin=='true' || $visitante==$propietario){
         echo " <a href='piece.php?delete=".$row->id."&id=$id#Mensajes' onclick='return confirmarmsg()'>eliminar</a>";
      }
      echo "<br>";
   }

   mysql_close();

   pie();
?>
