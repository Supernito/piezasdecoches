<?php
   include 'db.conf';
   include 'wrappers.php';

   cabecera("P�gina no encontrada");
?>
      <h3>404 Pieza not found!!</h3>
      <p>La p�gina o pieza que estabas buscando no existe actualmente o no ha existido nunca.</p>
      <br><img src="/img/404.png" alt="Coche desguazado" title="A este coche no le han dejado ni las ruedas"/><br>
      <p>Por favor, <a href="/default.php">vuelva al inicio</a> de la p�gina y realize otra b�squeda.</p>
      <a href="/default.php">Volver al inicio</a>
<?php
   pie();
?>
