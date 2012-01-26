<?php
   include 'db.conf';
   include 'wrappers.php';

   cabecera("Nueva pieza");
?>

<script type="text/javascript">
      msgerror="";
      function validaNombre(){
         var x=document.forms.formnuevapieza.nombre.value;
         if (x==null || x==""){
            msgerror+="\n    - El nombre no puede estar vacio.";
            return false;
         }
         return true;
      }

      function validaPrecio(){
         var x=document.forms.formnuevapieza.precio.value;
         if(x!=null && x!=""){
            if (isNaN(x)){
               msgerror+="\n    - El precio tiene que ser un numero, separando los centimos (si los hay) con un punto.";
               return false;
            }
         }
         return true;
      }

      function validaFormulario(){
         var validacionNombre=validaNombre();
         var validacionPrecio=validaPrecio();
         if(!validacionNombre || !validacionPrecio){
            alert("Se han producido los siguientes errores:"+msgerror);
            msgerror="";
            return false;               
         }else{
            return true;
         }
     }
</script>
<?php
   // Codigo quando inserten una pieza (solo si estan logueados)
   if(isset($_POST['enviar']) && $_SESSION['logged']==true) {
      mysql_connect(dbhost,dbuser,dbpass); 
      mysql_select_db(dbname);
      $propietario = $_SESSION['username'];
      if ($_FILES['imagen']['tmp_name']){
         $datosimg = $_FILES['imagen']['tmp_name'];
         $imagen = addslashes(@fread(fopen($datosimg, "r"), filesize($datosimg)));
      }
      $nombre = $_POST['nombre'];
      $descripcion = mysql_real_escape_string(trim(strip_tags(stripslashes($_POST['descripcion']))));
      $descripcion = str_replace('\\n', '<br>', $descripcion);
      $precio = $_POST['precio'];
      $coches = $_POST['coches'];

      // Control de la imagen
      if ($_FILES['imagen']['type'] == 'image/jpeg' || $_FILES['imagen']['type'] == 'image/jpg' ||
         $_FILES['imagen']['type'] == 'image/pjpeg' || $_FILES['imagen']['type'] == 'image/png' ||
         !$_FILES['imagen']['tmp_name']) {

         if ($_FILES['imagen']['size'] <= 1000000 || !$_FILES['imagen']['tmp_name']){
            $query = "INSERT INTO ".dbname.".pieza (nombre,descripcion,precio,coche,imagen,propietario)               
							 VALUES ('$nombre','$descripcion','$precio','$coches','$imagen','$propietario');";
            $res = mysql_query($query) or die(mysql_error());
            echo "Pieza insertada con éxito, Será moderada pronto.<BR><BR>";
         } else { // Imagen demasiado grande
            echo "La imagen es demasiado grande. No puede superar 1MB.<BR><BR>";
         }
      } else { // Imagen no es ni jpeg ni png
         echo "Solo se admiten imagenes .jpeg o .png<BR><BR>";
      }

      $query = "SELECT id from ".dbname.".usuario WHERE username='$propietario'";
      $res = mysql_query($query) or die(mysql_error());
      $row = mysql_fetch_array($res);
      echo "Ir a mi <a href='personal.php?id=".$row['id']."'>página personal</a><BR>";
      echo "<a href='newpiece.php'>Insertar otra pieza</a><BR>";
      echo "<a href='search.php'>Buscar pieza</a><BR>";
      echo "<BR><a href='./'>Volver al inicio</a>";

      mysql_close();

   } else {

      // Formulario para meter piezas
?>
   <form method="post" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" name="formnuevapieza" onsubmit="return validaFormulario();">
      <table border='0' cellspacing='0' summary='Formulario nueva pieza'>
      <tr><td>Nombre </td><td><input type='text' name='nombre' size='30' value=''></td></tr>
      <tr><td>Descripción </td><td><textarea rows="5" name="descripcion" cols="28"></textarea></td></tr>
      <tr><td>Precio </td><td><input type='text' name='precio' size='50' value=''></td></tr>
      <tr><td>Coches a los<BR>que se aplica </td><td><input type='text' name='coches' size='50' value=''></td></tr>
      <input type='hidden' name='Pieza_enviada'>
      <tr><td>Imagen </td><td><input type='file' name='imagen' size='50' value='imagen' accept='image/pjpeg,image/jpeg,image/png,image/jpg'>(Sólo formato .jpeg o .png e inferiores a 1MB)</td></tr>
      </table>
      <p>Si necesitas redimensionar la foto o cambiar el formato puedes hacerlo en
      <a target='_blank' href='http://www.picresize.com/'>Esta página</a></p>
      <p>Nota: El campo "Nombre" es obligatorio.</p>
      <input type='submit' value='enviar' name='enviar'>
   </form>

<?php

   }

   pie();
?>
