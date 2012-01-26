<?php
   function cabecera($titulo){
   echo "<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html lang='es' class='resizable'>
   <head>
      <title>Venta de piezas de coches - $titulo -</title>
      <meta http-equiv='Content-Type' content='text/html; charset= iso-8859-1'>
      <meta name='keywords'           content='piezas, coches, repuestos, recambios, comprar, vender'>
      <meta name='description'        content='Web para la compra y venta de piezas de coches y repuestos varios.'>
      <meta name='robots'             content='INDEX,FOLLOW,ARCHIVE'>
      <meta name='revisit-after'      content='7 days'>
      <link rel ='stylesheet' type='text/css' href='/css/estilo.css'>

      <script type='text/javascript'>
         function Registro() {
            window.location.href='register.php';
         }
		function Limpiausuario() {
            if (formlogin.username.value=='usuario') formlogin.username.value='';
         }
		function Limpiapass() {
            if (formlogin.password.value=='clave') formlogin.password.value='';
         }
      </script>

   </head>
   <body>
      <script type='text/javascript' language='javascript' src='js/jquery-1.5.js'></script>
      <script type='text/javascript' language='javascript' src='js/jquery.jfontsize-1.0.js'></script>
      <div id='cabecera'>
         <div id='QR_div'>
            <a id='QR_link' href='img/QR_large.png' type='image/png'><img id='QR_logo' src='img/QR_small.png' alt='Logo QR' title='Pulsa para ver el código QR más grande'></a>
         </div>
         <div id='fontsize_div'>
            <p>
            <a class='jfontsize-button' id='jfontsize-m' href='#'>A-</a> 
            <a class='jfontsize-button' id='jfontsize-d' href='#'>A</a> 
            <a class='jfontsize-button' id='jfontsize-p' href='#'>A+</a>
            </p>
         </div>
         <div id='titulo'>
            <h1><a class='link_titol' href='./'>PIEZAS DE COCHES</a></h1>
         </div>
         <div id='login'>";

   session_start();
   if ($_SESSION['logged']!=true) {
      //si no está logeado imprimimos el formulario

      echo "<form method='post' action='login.php' name='formlogin'>
      <input type='text'     name='username' size='5' maxlength='30' value='usuario' alt='Escribe tu nombre de usuario' title='Escribe tu nombre de usuario' onfocus='javscript:Limpiausuario();'>
      <input type='password' name='password' size='5' maxlength='30' value='clave' alt='Escribe tu clave' title='Escribe tu clave' onfocus='javscript:Limpiapass();'>
      <input type='submit'   value='login'       name='login'>
      <input type='button'   value='registrarse' name='register' onClick='javscript:Registro();'> </form>";
   }else{
      //si está logeado le saludamos :)
      echo "Hola, ".$_SESSION['username']." <a href='logout.php' class='resizable'>salir</a>";
   }

   echo"</div></div>
         <script type='text/javascript' language='javascript'>
          $('.resizable').jfontsize({
          btnMinusClasseId: '#jfontsize-m',
          btnDefaultClasseId: '#jfontsize-d',
          btnPlusClasseId: '#jfontsize-p'
          });
       </script>
      <div id ='contenido'>";
   }    

   function pie(){
      echo "<div id='pie'><center><BR>";
      echo "<button type='button' onClick=\"location.href='http://www.gnu.org/copyleft/gpl.html'\">
               Licencia
            </button> ";
      echo "<button type='button' onClick=\"location.href='https://github.com/Supernito/piezasdecoches'\">
               Descargar
            </button> ";
      echo "<button type='button' onClick=\"location.href='/motivational.php'\">
               Agradecimientos y motivación
            </button> ";
      echo "<button type='button' onClick=\"location.href='/faq.php'\">
               FAQ
            </button> ";
      echo "<button type='button' onClick=\"location.href='/disclaimers.php'\">
               Disclaimers
            </button>";
      echo "</center></div>
      </div>
   </body>
</html>";

   }
?>
