<?php
   include 'db.conf';
   include 'wrappers.php';

   mysql_connect(dbhost,dbuser,dbpass);
   mysql_select_db(dbname);
   session_start();

   $id = $_GET['id'];                  // Identificador del propietario de la pàgina
   $query = "SELECT username, email, zona_h, is_admin FROM ".dbname.".usuario WHERE id='$id'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);
   $propietario = $row['username'];    // Nombre del propietario de la página
   $correo = $row['email'];            // Correo del propietario de la página
   $zona_horaria = $row['zona_h'];     // Zona horaria del propietario de la pàgina
   $admin = $row['is_admin'];          // True si es administrador el propietario de la página
   $visitante = $_SESSION['username']; // Si esta logueado contiene el nombre del visitante (no necesariamente igual al propietario)
   $query = "SELECT is_admin,zona_h FROM ".dbname.".usuario WHERE username='$visitante'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);
   $visit_admin = $row['is_admin'];    // Si no estaba logueado este campo estará vacio
   if ($_SESSION['logged']=='true'){
      $zona_horaria = $row['zona_h'];  // Zona horaria del visitante si está logueado
   }else{
      $zona_horaria = "+00:00";        // Zona horaria del servidor si el visitante no está logueado
   }
   $query = "SELECT count(*) comen FROM ".dbname.".mensaje m WHERE m.usuario='$propietario'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);
   $comentarios = $row['comen'];       // Número de comentarios que ha hacho el propietario
   $query = "SELECT count(*) piezas FROM ".dbname.".pieza p WHERE p.propietario='$propietario'";
   $res = mysql_query($query) or die(mysql_error());
   $row = mysql_fetch_array($res);
   $piezas = $row['piezas'];           // Número de piezas que ha publicado el propietario
   

   if ($visitante==$propietario){
      cabecera("..:: HOME ::..");
   } else {
      cabecera("Página de $propietario");
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

   echo "<H4>Página personal de $propietario</H4>";
   echo "Número de usuario: $id <BR>";
   echo "Correo: ";
   if ($_SESSION['logged']=='true'){
      echo $correo."<BR>";
   } else {
      echo "Logueate para ver el correo<BR>";
   }
   echo "<a href='personal.php?id=$id#Piezas'>Piezas:</a> $piezas <BR>";
   echo "<a href='personal.php?id=$id#Mensajes'>Comentarios:</a> $comentarios <BR>";
   if ($admin=='true'){
      echo "Este usuario es ADMINISTRADOR";
      if ($visitante==$propietario){
         echo "<BR><a href='controlcenter.php'>Centro de control</a>";
      }
   }

// Piezas

   echo "<HR noshade><H4><span id='Piezas'>Piezas de $propietario:</span></H4>";

   // Formulario para insertar piezas
   if ($visitante==$propietario){
      echo "<form action='newpiece.php' method='POST'> 
               <input type='submit' class='button' name='nuevapieza' value='Introducir nueva pieza'/>
            </form>";
      echo "<HR align='center' width='95%'>";
   }

   // Imprime las piezas
   $primera_pieza=1;

   $query = "SELECT * FROM ".dbname.".pieza where propietario='$propietario' ORDER BY id DESC";
   $res = mysql_query($query) or die(mysql_error());
   while ($row=mysql_fetch_array($res)){
      if ($primera_pieza==1) {$primera_pieza=0;}else{echo "<HR align='center' width='95%'>";}
      //imagen
      $idpieza = $row['id'];
      echo "<table border='0' summary='Tabla que contiene la imagen y los datos de una pieza'><tr><td>";
      if ($row['imagen']){
         echo "<a href='showimg.php?id=".$row['id']."'> <img src='showimg.php?id=".$row['id']."' alt='Imagen de la pieza' height=160 Width=130></a><br>";
      }
      echo "</td><td>";
      //datos de la pieza
      echo "Nombre de la pieza: <a target='_blank' href='/piece.php?id=".$row['id']."'>".$row['nombre']."</a><br>";
      echo "Coches a los que se aplica: ".$row['coche']."<BR>";
      echo "Precio: ".$row['precio']."<BR>";
      echo "Descripción:<BR> ".$row['descripcion']."<BR>";

      //opciones de edición, solo para el dueño de la pieza y administradores
      if ($visitante==$propietario || $visit_admin=='true'){
          echo "<a href='deletepiece.php?idpieza=$idpieza' onclick='return confirmarpz()'>Eliminar pieza</a>";
      }
      echo "</td></tr></table>";
   }

// Mensajes

   echo "<HR noshade><H4><span id='Mensajes'>Mensajes dirigidos a $propietario:</span></H4>";

   // Formulario para insertar mensajes
   if ($_SESSION['logged']=='true'){
      echo "<form action='personal.php?id=$id#Mensajes' method='POST'> Tu comentario:
            <input class='textfield' name='comment' type='text' value=''  maxlength='255'/>&nbsp;
            <input type='submit' class='button' name='Introducir' value='Introducir'/>
            </form>";
   } else {
      echo "Logueate para poder escribir a $propietario";
   }
   echo "<HR align='center' width='95%'>";

   // Se inserta el mensaje
   if ($_SESSION['logged']=='true' && isset($_POST['comment']) && trim($_POST['comment']) != "") {
      $comment = mysql_real_escape_string(trim(strip_tags(stripslashes($_POST['comment']))));
      if (strlen($comment > 255)) $comment = substr($comment,0,255);

      if (!empty($comment)) {
          $query = "INSERT INTO ".dbname.".mensaje (usuario,mensaje,dia,pieza) VALUES ('$visitante','$comment',NOW(),'-$id')";
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
$query="SELECT usuario, u.id id_usr, mensaje, m.id id_msg,CONVERT_TZ(dia, '+00:00', '$zona_horaria') as dia_local
      FROM ".dbname.".mensaje m left join ".dbname.".usuario u on (m.usuario=u.username)
      WHERE pieza='-$id' ORDER BY dia DESC";
   $res = mysql_query($query) or die(mysql_error());
   while ($row = mysql_fetch_object($res)){
      echo "En ".$row->dia_local." el usuario
            <a target='_blank' href='/personal.php?id=".$row->id_usr."'> ".$row->usuario.
           "</a> dijo: ".$row->mensaje;
      if ($visit_admin=='true' || $visitante==$propietario){
         echo " <a href='personal.php?delete=".$row->id_msg."&id=$id#Mensajes' onclick='return confirmarmsg()'>eliminar</a>";
      }
      echo "<br>";
   }

   mysql_close();

   pie();
?>
