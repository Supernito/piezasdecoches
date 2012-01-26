<?php
   include 'db.conf';
   include 'wrappers.php';

   define (RES_X_PAG,'10'); // Resultados por página

   cabecera("Búsqueda");

   session_start();

   mysql_connect(dbhost,dbuser,dbpass);
   mysql_select_db(dbname);

   // Formulario
   if ($_GET['Busqueda_activa'] != 'true'){

?>

   <script type="text/javascript">
      function busquedaValida(){
         if(document.searchForm.Nombre.checked      == false &&
            document.searchForm.Descripcion.checked == false &&
            document.searchForm.Coches.checked      == false){

            alert("Debes seleccionar almenos un campo al que buscar");
            return false;               
         }else{
            return true;
         }
      }
   </script>

   <H4>Introuzca los parámetros de búsqueda</H4>
   <form method="get" action="/search.php" name="searchForm" onsubmit="return busquedaValida();">
      Palabra a buscar: <input type="text" name="search" size="30" value=""><BR>
      En los campos:<BR>
      <input type="checkbox" name="Nombre"          checked="checked">Nombre<BR>
      <input type="checkbox" name="Descripcion"     checked="checked">Descripción<BR>
      <input type="checkbox" name="Coches"          checked="checked">Coches<BR>
      <input type="hidden"   name="Busqueda_activa" value="true">
      <input type="submit"   name="Buscar">
      <input type="hidden"   name="Pag"             value="1">
   </form>
<?php
   } else {

      echo "<H4>Resultados</H4>";
      echo "<a href='/search.php'>Nueva búsqueda</a>";
      echo "<HR noshade>";

      // Montamos la consulta
      $cond = "false"; // Condición solo para evitar un and suelto al final
      $url  = "search.php?search=".$_GET['search']."&";
      if($_GET['Nombre']){
         $cond = "upper(p.nombre) like upper('%".$_GET['search']."%') or ".$cond;
         $url  = $url."Nombre=on&";
      }
      if($_GET['Descripcion']){
         $cond = "upper(p.descripcion) like upper('%".$_GET['search']."%') or ".$cond;
         $url  = $url."Descripcion=on&";
      }
      if($_GET['Coches']){
         $cond = "upper(p.coche) like upper('%".$_GET['search']."%') or ".$cond;
         $url  = $url."Coches=on&";
      }

      $url = $url."Busqueda_activa=true&Buscar=Enviar&Pag=";

      // Contamos los resultados para poder paginar
      $query = "SELECT count(*) num from ".dbname.".pieza p where $cond and moderada=1";
      $res   = mysql_query($query) or die(mysql_error);
      $row   = mysql_fetch_array($res);
      $num   = $row['num'];
      $skip  = 0;
      if ($num == 0){
         echo "No se han encontrado resultados.";
      } else {
         if (is_numeric($_GET['Pag']) && (int)$_GET['Pag'] > 0 ){
            $skip = ((int)$_GET['Pag'] - 1) * RES_X_PAG;
            $lim  = (int)$_GET['Pag'] * RES_X_PAG;
            $num_pags = ceil($num / RES_X_PAG);
            echo "<center>";
            for ($i=1; $i <= $num_pags; $i++){
               if ((int)$_GET['Pag'] != $i) echo " <a href='$url$i'>$i</a> ";
               else echo " $i ";
            }
            echo "</center>";
         } else {
            echo "<center>No se ha tenido en cuenta la paginación!</center>";
         }
      }

      $query = "SELECT * FROM ".dbname.".pieza p where $cond ORDER BY p.id LIMIT $skip,".RES_X_PAG;
      $res   = mysql_query($query) or die(mysql_error());

      $primera_pieza=1;
      while ($row = mysql_fetch_array($res)) {
         if ($primera_pieza==1) {$primera_pieza=0;}else{echo "<HR align='center' width='95%'>";}
         // imagen
         $idpieza = $row['id'];
         echo "<table border='0' summary='Tabla que contiene la imagen y los datos de una pieza'><tr><td>";
         if ($row['imagen']){
            echo "<a href='/showimg.php?id=".$row['id']."'> <img src='/showimg.php?id=".$row['id']."' alt='Imagen de la pieza' height=160 Width=130></a><br>";
         }
         echo "</td><td>";
         //datos de la pieza
         echo "Nombre de la pieza: <a target='_blank' href='/piece.php?id=".$row['id']."'>".$row['nombre']."</a><br>";
         echo "Coches a los que se aplica: ".$row['coche']."<br>";
         echo "Precio: ".$row['precio']."<br>";
         $query = mysql_query("SELECT id,email FROM ".dbname.".usuario WHERE username='".$row['propietario']."'");
         $rowowner = mysql_fetch_array($query); // Estaría bé posar això en un sol select
         echo "Propietario: <a target='_blank' href='/personal.php?id=".$rowowner['id']."'>".$row['propietario']."</a><BR>";
         echo "Correo:";
         if ($_SESSION['logged']=='true'){
            echo $rowowner['email']."<BR>";
         } else {
            echo "Logueate para ver el correo<BR>";
         }
         echo "Descripción:<BR> ".$row['descripcion']."<br>";
         echo "</td></tr></table>";
      }
      if (is_numeric($_GET['Pag']) && (int)$_GET['Pag'] > 0 ){
         echo "<center>";
         for ($i=1; $i <= $num_pags; $i++){
            if ((int)$_GET['Pag'] != $i) echo " <a href='$url$i'>$i</a> ";
            else echo " $i ";
         }
         echo "</center>";
      } else {
         echo "<center>No se ha tenido en cuenta la paginación!</center>";
      }
      echo "<HR noshade>";
      echo "<a href='search.php'>Nueva búsqueda</a>";
   }

   mysql_close();

   pie();
?>
