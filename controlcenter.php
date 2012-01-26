<?php
   include 'db.conf';
   include 'wrappers.php';

   cabecera("Centro de control");

   // Script para cambiar el color de las filas
?>

   <script type="text/javascript">
      var tmp;
      function resaltaLinia(row) {
         tmp = row.style.backgroundColor
         row.style.backgroundColor = "#a0a0a0";
      }
      function restauraLinia(row){
         row.style.backgroundColor = tmp;
      }
   </script>

<?php

   session_start();

   mysql_connect(dbhost,dbuser,dbpass);
   mysql_select_db(dbname);

   $query = "SELECT is_admin FROM ".dbname.".usuario WHERE username='".$_SESSION['username']."'";
   $res   = mysql_query($query) or die(mysql_error());
   $row   = mysql_fetch_array($res);

   if($_SESSION['logged'] == 'true' && $row['is_admin'] == 'true'){

      // Miramos si se han de cambiar los permisos a alguien
      if (isset($_GET['c'])){
         $query = "SELECT is_admin,username FROM ".dbname.".usuario WHERE id=".$_GET['c'];
         $res   = mysql_query($query) or die(mysql_error());
         $row   = mysql_fetch_array($res);
         if ($row['username'] == $_SESSION['username']){
            echo "Serà mejor que no te cambies los permisos o tendras problemas.<BR>";
         } else {
            if ($row['is_admin'] == 'true'){
               $query = "UPDATE ".dbname.".usuario SET is_admin='false' where id=".$_GET['c'];
            } else {
               $query = "UPDATE ".dbname.".usuario SET is_admin='true' where id=".$_GET['c'];
            }
            $res = mysql_query($query) or die(mysql_error());
         }
      }

      // Aceptar una pieza
      if (isset($_GET[aceptar])){
         $query = "UPDATE ".dbname.".pieza SET moderada=1 WHERE id=".$_GET[aceptar];
         $res   = mysql_query($query) or die(mysql_error());
      }

      // Para cambiar la contraseña (Paso 1)
      if (isset($_GET['P1'])){
         $query = "SELECT username FROM ".dbname.".usuario WHERE id=".$_GET['P1'];
         $res   = mysql_query($query) or die(mysql_error());
         $row   = mysql_fetch_array($res);
         echo "<H4>Centro de Control: Cambiar clave para el usuario \"".$row['username']."\"</H4>";
         echo "<form method='post' action='/controlcenter.php' name='CCpassForm'>
                  <input type='hidden'   name='P2' value='".$_GET['P1']."'>
                  Escribe la nueva contraseña
                  <input type='password' name='newPass' size='30' value=''>
                  <input type='submit'   name='Cambiar' value='Cambiar'>
               </form>";
      }

      // Para cambiar la contraseña (Paso 2)
      if (isset($_POST['P2'])){
         $pass  = md5($_POST['newPass']);
         $query = "UPDATE ".dbname.".usuario SET password='$pass' where id=".$_POST['P2'];
         $res   = mysql_query($query) or die(mysql_error());
         echo "Contraseña cambiada con éxito<BR>";
      }

      // Borrar un usuario
      if (isset($_GET['d'])){
         $query = "SELECT username FROM ".dbname.".usuario WHERE id=".$_GET['d'];
         $res   = mysql_query($query) or die(mysql_error());
         $row   = mysql_fetch_array($res);
         if ($row['username'] == $_SESSION['username']){
            echo "Serà mejor que no te borres a ti mismo.<BR>";
         } else {
            // Mensajes que ha escrito el
            $usuario = $row['username'];
            $query = "DELETE FROM ".dbname.".mensaje WHERE usuario='$usuario'";
            $res   = mysql_query($query) or die(mysql_error());

            // Mensajes dirigidos a el
            $query = "DELETE FROM ".dbname.".mensaje WHERE pieza='-$_GET[d]'";
            $res   = mysql_query($query) or die(mysql_error());

            // Piezas que ha puesto
            $query = "DELETE FROM ".dbname.".pieza WHERE propietario='$usuario'";
            $res   = mysql_query($query) or die(mysql_error());

            // Usuario en si
            $query = "DELETE FROM ".dbname.".usuario WHERE id=".$_GET['d'];
            $res   = mysql_query($query) or die(mysql_error());

            echo "El usuario \"$usuario\" ha sido borrado.";
         }
      }

      // USUARIOS
      echo "<H4>Centro de Control: Usuarios</H4>";
      echo "<table border='1' cellspacing='0' summary='Control de usuarios'>";
      echo "<tr>
               <td>
                  <b><center>Id</center></b>
               </td>
               <td>
                  <b><center>Nombre</center></b>
               </td>
               <td>
                  <b><center>Correo</center></b>
               </td>
               <td>
                  <b><center>Administrador</center></b>
               </td>
               <td>
                  <b><center>Contraseña</center></b>
               </td>
               <td>
                  <b><center>Eliminar</center></b>
               </td>
            </tr>";

      $query = "SELECT id,username,email,is_admin FROM ".dbname.".usuario order by id";
      $res   = mysql_query($query) or die(mysql_error());
      while ($row = mysql_fetch_array($res)) {
         echo "<tr onMouseOver='resaltaLinia(this)' onMouseOut='restauraLinia(this)'>";
         echo "   <td><center>$row[id]</center></td>";
         echo "   <td><center>$row[username]</center></td>";
         echo "   <td><center>$row[email]</center></td>";
         echo "   <td><center>$row[is_admin]";
         echo " <a href='/controlcenter.php?c=$row[id]'>cambiar</a></center></td>";
         echo "   <td><center><a href='/controlcenter.php?P1=$row[id]'>nueva</a></center></td>";
         echo "   <td><center><a href='/controlcenter.php?d=$row[id]'>eliminar</a></center></td>";
         echo "</tr>";
      }
      echo "</table>";

      // MODERACION
      echo "<H4>Centro de Control: Moderación de piezas</H4>";
      $query = "SELECT * FROM ".dbname.".pieza WHERE moderada='0' ORDER BY id DESC";
      $res = mysql_query($query);
      while ($row=mysql_fetch_array($res)){
         echo "<HR align='center' width='95%'>";
         //imagen
         $idpieza = $row['id'];
         echo "<table border='0' summary='Tabla que contiene la imagen y los datos de una pieza'><tr>";
         echo "<td>";
         if ($row['imagen']){
            echo "<a href='showimg.php?id=$idpieza'> <img src='showimg.php?id=".$row['id']."' alt='Imagen de la pieza' height=160 Width=130></a><br>";
         }
         echo "</td><td>";
         //datos de la pieza
         echo "Nombre de la pieza: <a target='_blank' href='/piece.php?id=$idpieza'>".$row['nombre']."</a><br>";
         $propietario = $row['propietario'];
         $query = mysql_query("SELECT id,email FROM ".dbname.".usuario WHERE username='$propietario'");
         $rowowner = mysql_fetch_array($query); // Estaría bé posar això en un sol select
         echo "Coches a los que se aplica: ".$row['coche']."<br>";
         echo "Precio: ".$row['precio']."<br>";
         echo "Propietario: <a target='_blank' href='/personal.php?id=".$rowowner['id']."'>".$propietario."</a><br>";
         echo "Correo: ";
         echo $rowowner['email']."<BR>";
         echo "Descripción:<BR> ".$row['descripcion']."<br>";
         //opciones de edición, solo para el dueño de la pieza y administradores
         echo "<a href='deletepiece.php?idpieza=$idpieza' onclick='return confirmarpz()'>Eliminar pieza</a><BR>";
         echo "<a href='/controlcenter.php?aceptar=$idpieza'>Aceptar</a>";
         echo "</td></tr></table>";
      }

   } else {
      echo "No estas autorizado a entrar aquí<BR>";
   }

   echo "<BR><a href='./'>Volver al inicio</a>";

   mysql_close();

   pie();
?>
