<?php
   include 'db.conf';
   include 'wrappers.php';

   define (MAX_MSG,'10');
   define (MAX_ITM,'10');

   cabecera("..::PAGINA PRINCIPAL::..");

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
   mysql_connect(dbhost,dbuser,dbpass);
   mysql_select_db(dbname);
   session_start();

   if ($_SESSION['logged']!=true){
      // Introducción de lo que hacela web
?>
   <h4> Bienvenido a la web PIEZAS DE COCHES, una web que simplemente intenta poner
 en contacto a compradores y vendedores de piezas de coches. Para más información
 visita nuestra sección de <a href='/faq.php'>ayuda</a> o usa nuestro
 <a href='/search.php'>buscador</a> para encontrar lo que buscas. Muchas gracias.</h4>  

<?php
   } else {
      // Si esta logueado ya sabe que hace la web
      // Obtenemoslos datos
      $zona_horaria="+00:00";
      $loggedu = $_SESSION['username'];
      $query = "SELECT id,is_admin,zona_h FROM ".dbname.".usuario WHERE username='$loggedu'";
      $res = mysql_query($query) or die(mysql_error());
      $row = mysql_fetch_array($res);
      $id_usuario = $row['id'];
      $isadmin = $row['is_admin'];
      $zona_horaria = $row['zona_h'];

      // Y le enseñamos las opciones que tiene
      echo "Bienvenido de nuevo $loggedu, ahora puedes:";
      echo " ir a tu <a href='/personal.php?id=$id_usuario'>página</a>, ";
      echo "<a href='newpiece.php'>insertar pieza</a>, ";
      echo "<a href='search.php'>buscar piezas</a> ";
      echo "o <a href='/logout.php'>salir</a>";
   }

// Mensajes

   echo "<HR noshade><H4>Que dicen nuestros usuarios</H4>";

   // Formulario para insertar mensajes
   if ($_SESSION['logged']=='true'){
      echo "<form action='default.php' method='POST'> Tu comentario:
               <input class='textfield' name='comment' type='text' value=''  maxlength='255'/>&nbsp;
               <input type='submit' class='button' name='Introducir' value='Introducir'/>
            </form>";
   } else {
      echo "Logueate para poder comentar";
   }
   echo "<HR align='center' width ='95%'>";

   // Se inserta el mensaje
   if ($_SESSION['logged']=='true' && isset($_POST['comment']) && trim($_POST['comment']) != ""){
      $comment = mysql_real_escape_string(trim(strip_tags(stripslashes($_POST['comment']))));
      if (strlen($comment > 255)) $comment = substr($comment,0,255);

      if (!empty($comment)) {
         $query = "INSERT INTO ".dbname.".mensaje (usuario,mensaje,dia) VALUES ('$loggedu','$comment',NOW())";
         $res = mysql_query($query) or die(mysql_error());
      }
   }

   // Se borra el mensaje
   if ($isadmin=='true' && isset($_GET['delete']) && is_numeric($_GET['delete'])) {
      $id = (int)$_GET['delete'];
      $query = "DELETE FROM ".dbname.".mensaje WHERE id='$id'";
      mysql_query($query) or die(mysql_error());
   }

   // Visualizamos los mensajes
   $query="SELECT usuario, u.id id_usr, mensaje, m.id id_msg,CONVERT_TZ(dia, '+00:00', '$zona_horaria') as dia_local
      FROM ".dbname.".mensaje m left join ".dbname.".usuario u on (m.usuario=u.username)
      WHERE pieza=0 ORDER BY dia DESC LIMIT 0,".MAX_MSG;
   $res = mysql_query($query) or die(mysql_error());;
   while ($line = mysql_fetch_object($res)) {
      echo "En ".$line->dia_local." el usuario
            <a href='/personal.php?id=".$line->id_usr."'>".$line->usuario.
           "</a> dijo: ".$line->mensaje;
      if ($isadmin=='true'){
         echo " <a href='default.php?delete=".$line->id_msg."'onclick='return confirmarmsg()'>eliminar</a>";
      }
      echo "<br>";
   }

// Piezas

   echo "<HR noshade><H4>Las últimas piezas de nuestros usuarios</H4>";

   // Formulario para insertar piezas
   if ($_SESSION['logged']=='true'){
      echo "<form action='newpiece.php' method='POST'> 
               <input type='submit' class='button' name='nuevapieza' value='Introducir nueva pieza'/>
            </form>";
   } else {
      echo "Logueate para poder insertar piezas";
   }

   //en esta parte del código se imprimen las piezas
   $query = "SELECT * FROM ".dbname.".pieza WHERE moderada=1 ORDER BY id DESC LIMIT 0,".MAX_ITM;
   $res = mysql_query($query);
   while ($row=mysql_fetch_array($res)){
      echo "<HR align='center' width='95%'>";
      //imagen
      $idpieza = $row['id'];
      echo "<table border='0' summary='Tabla que contiene la imagen y los datos de una pieza'><tr>";
      echo "<td>";
      if ($row['imagen']){
         echo "<a href='showimg.php?id=$idpieza'> <img src='showimg.php?id=$row[id]' alt='imagen de \"$row[nombre]\"' height=160 Width=130></a><br>";
      }
      echo "</td><td>";
      //datos de la pieza
      echo "Nombre de la pieza: <a href='/piece.php?id=$idpieza'>".$row['nombre']."</a><br>";
      $propietario = $row['propietario'];
      $query = mysql_query("SELECT id,email FROM ".dbname.".usuario WHERE username='$propietario'");
      $rowowner = mysql_fetch_array($query); // Estaría bé posar això en un sol select
      echo "Coches a los que se aplica: ".$row['coche']."<br>";
      echo "Precio: ".$row['precio']."<br>";
      echo "Propietario: <a href='/personal.php?id=".$rowowner['id']."'>".$propietario."</a><br>";
      echo "Correo: ";
      if ($_SESSION['logged']=='true'){
         echo $rowowner['email']."<BR>";
      } else {
         echo "Logueate para ver el correo<BR>";
      }
      echo "Descripción:<BR> ".$row['descripcion']."<br>";
      //opciones de edición, solo para el dueño de la pieza y administradores

      if ($loggedu==$propietario || $isadmin=='true'){
          echo "<a href='deletepiece.php?idpieza=$idpieza' onclick='return confirmarpz()'>Eliminar pieza</a>";
      }
      echo "</td></tr></table>";
   }

   mysql_close();

  pie();
?>
