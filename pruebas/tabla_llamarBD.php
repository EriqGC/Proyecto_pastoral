<html>
	<head>
		<title>Ejemplo PHP y MySQL consulta a la Base de Datos</title>
	</head>
	<body> 
	   <H2>Ejemplo consulta Base de Datos mibd con imagenes</H2> 
       <?php   // consultaimg.php
		     include "conex.php";
		     $link=Conectarse();
		     $result=mysqli_query($link,"select * from articulo");
	   ?>
	   <TABLE BORDER="1" CELLSPACING="1" CELPADDING="1" align="center" width="40%">
	   <TR><TD>Id Art&iacute;culo</TD>
	       <TD>Nombre</TD>
	       <TD>Imagen</TD>
	   </TR>
	     <?php  
			while($row = mysqli_fetch_array($result))
			{   $var= "<img src='imagenes/" . $row['archivo'] . "' width=80>";
				printf("<TR><TD>%d</TD><TD>%s</TD>
				<TD>%s</TD></TR>",
				$row["Id_articulo"],$row["titulo"],$var);
			}
			mysqli_free_result($result);
			mysqli_close($link);
		?>	
      </TABLE>
   </body>
</html>