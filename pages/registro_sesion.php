<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Sesion</title>
    <link rel="stylesheet" href="../assets/css/registro.css">
</head>
<body>
    <main>
        <img src="../assets/img/fondo.png" alt="fondo" id="imagen-fondo">
        <div class="back_overlay"></div>
        <div class="seccion-superior" style="display: block;">  
            <div class="contenedor-logo">
            <img src="..\assets\img\Logotipo-UPAEP-color.png" alt="upaep-logo" id="logo-header">
            </div>

            <div class="overlay">
            
                <div class="contenedor-registro">
                    <div id="titulo">
                        <H1>Crear una cuenta</H1>
                    </div>
                    <form name="form" id="form" method="POST" action="../includes/guardar_datos.php">
                        <!--Tabla de registro 1-->    
                        <table class="tabla-registro" id="tabla-registro1">
                            <colgroup>
                                <col class="columna-izquierda">
                                <col class="columna-derecha">
                            </colgroup>
                            <tr class="indicaciones">
                                <td>*Nombre(s)</td>
                                <td>*Apellido Paterno</td>
                            </tr>
                            <tr>
                                <td><input required type="text" name="nombre_alumno" id="nombre_alumno" placeholder="Nombre(s)" class="entrada1"></td>
                                <td><input required type="text" name="apellido_p" id="apellido_p" placeholder="Apellido Paterno" class="entrada1"></td>
                            </tr>
                            <tr class="indicaciones">
                                <td>*Apellido Materno</td>
                                <td>*Fecha de Nacimiento</td>
                            </tr>
                            <tr>
                                <td><input required type="text" name="apellido_m" id="apellido_m" placeholder="Apellido Materno" class="entrada1"></td>
                                <td>
                                    <table>
                                        <tr>
                                            <td><input required type="number" name="dia_n" id="dia_n" placeholder="Dia" class="entrada_d"></td>
                                            <td><input required type="number" name="mes_n" id="mes_n" placeholder="Mes" class="entrada_m"></td>
                                            <td><input required type="number" name="anio_n" id="anio_n" placeholder="Año" class="entrada_a"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="indicaciones">
                                <td colspan="2">*Sexo</td>
                            </tr>
                            <tr>
                                <td>
                                    <table>
                                        <tr class="interes">
                                            <td><input required type="radio" name="sexo" value="hombre">Hombre</td>
                                            <td><input required type="radio" name="sexo" value="mujer">Mujer</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table width="80%">
                                        <tr>
                                            <td width="40%"><input type="button" name="cancelar" id="cancelar" value="Cancelar" class="cancelar"></td>
                                            <td><input type="button" name="siguiente1" id="siguiente1" value="Siguiente" class="siguiente"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 1em;"></td>
                                <td style="font-size: 1vw !important; max-width: 100%;">¿Ya tienes una cuenta? <a href="inicio_sesion.php">Inicia Sesión</a></td>
                            </tr>
                        </table>
                    

                        <!--Tabla de registro 2-->             
                        <table class="tabla-registro" id="tabla-registro2" style="display: none">
                            <colgroup>
                                <col class="columna-izquierda">
                                <col class="columna-derecha">
                            </colgroup>
                            <tr class="indicaciones">
                                <td>Enfermedades</td>
                                <td>Alergias</td>
                            </tr>
                            <tr>
                                <td><input type="text" name="enfermedades" id="enfermedades" placeholder="Enfermedades" class="entrada1"></td>
                                <td><input type="text" name="alergias" id="alergias" placeholder="Alergias" class="entrada1"></td>
                            </tr>
                            <tr class="indicaciones">
                                <td colspan="2">Tratamiento</td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="text" name="tratamiento" id="tratamiento" placeholder="Tratamiento" class="entrada_tratamiento"></td>
                            </tr>
                            <tr class="indicaciones">
                                <td colspan="2">*Número de emergencia</td>
                            </tr>
                            <tr>
                                <td><input required type="number" name="numero_emergencias" id="numero_emergencias" placeholder="22-22-22" class="entrada1"></td>
                                <td>
                                    <table width="80%">
                                        <tr>
                                            <td><input type="button" name="atras2" id="atras2" value="Atras" class="cancelar"></td>
                                            <td><input type="button" name="siguiente2" id="siguiente2" value="Siguiente" class="siguiente"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 1em;"></td>
                                <td style="font-size: 1vw !important; max-width: 100%;">¿Ya tienes una cuenta? <a href="inicio_sesion.php">Inicia Sesión</a></td>
                            </tr>
                        </table>

                        <!--Tabla de registro 3-->
                        <table class="tabla-registro" id="tabla-registro3" style="display: none">
                            <colgroup>
                                <col class="columna-izquierda3">
                                <col class="columna-media3">
                                <col class="columna-derecha3">
                            </colgroup>
                            <tr class="indicaciones">
                                <td colspan="3">*Selecciona todas las habilidades<br>que tengas:<br><br></td>
                            </tr>
                            <tr class="interes">
                                <td><input type="checkbox" name="habilidades[]" id="cantar">Cantar</td>
                                <td><input type="checkbox" name="habilidades[]" id="bailar">Bailar</td>
                                <td><input type="checkbox" name="habilidades[]" id="instrumento">Tocar un instrumento</td>
                            </tr>
                            <tr class="interes">
                                <td><input type="checkbox" name="habilidades[]" id="liderar">Liderar</td>
                                <td><input type="checkbox" name="habilidades[]" id="comunicar">Comunicar</td>
                                <td><input type="checkbox" name="habilidades[]" id="aconsejar">Aconsejar</td>
                            </tr>
                            <tr class="interes">
                                <td><input type="checkbox" name="habilidades[]" id="reparar">Reparar</td>
                                <td><input type="checkbox" name="habilidades[]" id="ninguna">Ninguna</td>
                                <td><input type="checkbox" name="habilidades[]" id="otra">Otra</td>
                            </tr>
                            <tr class="indicaciones">
                                <td colspan="2"><br>*Talla de camisa</td>
                            </tr>
                            <tr>
                                <td>
                                    <select id="playera" name="playera" class="entrada_a">
                                        <option value="Elegir" selected disabled>Elegir</option>
                                        <option value="Chica">Chica</option>
                                        <option value="Mediana">Mediana</option>
                                        <option value="Grande">Grande</option>
                                    </select>
                                </td>        
                                <td colspan="3">
                                    <table width="50%">
                                        <tr class="botones">
                                            <td><input type="button" name="atras3" id="atras3" value="Atras" class="cancelar"></td>
                                            <td><input type="button" name="siguiente3" id="siguiente3" value="Siguiente" class="siguiente"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 1em;"></td>
                                <td style="font-size: 1vw !important; max-width: 100%;">¿Ya tienes una cuenta? <a href="inicio_sesion.php">Inicia Sesión</a></td>
                            </tr>
                        </table>

                        <!--Tabla de registro 4-->
                        <table class="tabla-registro" id="tabla-registro4" style="display: none">
                            <colgroup>
                                <col class="columna-izquierda">
                                <col class="columna-derecha">
                            </colgroup>
                            <tr class="indicaciones">
                                <td>*Residencia Origen</td>
                                <td>*Residencia Actual</td>
                            </tr>
                            <tr>
                                <td><input required type="text" name="residencia_o" id="residencia_o" placeholder="Residencia Origen" class="entrada1"></td>
                                <td><input required type="text" name="residencia_a" id="residencia_a" placeholder="Residencia Actual" class="entrada1"></td>
                            </tr>
                            <tr class="indicaciones">
                                <td colspan="2"><br>*Selecciona el mensaje con el que más te identifiques:</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                <select required id="mensaje" name="mensaje" class="entrada_tratamiento">
                                    <option value="" selected disabled>Elegir</option>
                                    <option value="mensaje1">algo1</option>
                                    <option value="mensaje2">algo2</option>
                                    <option value="mensaje3">algo3</option>
                                </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="invitado">
                                    <input required type="radio" name="invitado" id="estudianteopc" value="estudiante" style="transform: scale(2);"> Formas parte de la comunidad UPAEP
                                </td>
                    
                                      
                                <td>
                                    <table style="width: 80%; margin-top: 5%;">
                                        <tr class="botones">
                                            <td><input type="button" name="atras4" id="atras4" value="Atras" class="cancelar"></td>
                                            <td><input type="button" name="siguiente4" id="siguiente4" value="Siguiente" class="siguiente"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="invitado" style="padding-bottom: 1em;">
                                    <input required type="radio" name="invitado" id="invitadoopc" value="invitado" style="transform: scale(2);"> Fuiste invitado por alguien de UPAEP
                                </td>

                                <td style="font-size: 1vw !important; max-width: 100%;">¿Ya tienes una cuenta? <a href="inicio_sesion.php">Inicia Sesión</a></td>
                            </tr>
                        </table>

                        <!--Tabla de registro estudiante-->
                        <table class="tabla-registro" id="tabla-registro5" style="display: none">
                            <colgroup>
                                <col class="columna-izquierda">
                                <col class="columna-derecha">
                            </colgroup>
                            <tr class="indicaciones">
                                <td>*ID UPAEP</td>
                                <td>*Matricula UPAEP</td>
                            </tr>
                            <tr>
                                <td><input required type="text" name="idupaep" id="idupaep" placeholder="ID" class="entrada1"></td>
                                <td><input required type="text" name="matricula" id="matricula" placeholder="Matricula" class="entrada1"></td>
                            </tr>
                            <tr class="indicaciones">
                                <td colspan="2">*Licenciatura</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><input required type="text" name="licenciatura" id="licenciatura" placeholder="Licenciatura" class="entrada1"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <table width="80%">
                                        <tr>
                                            <td width="40%"><input type="button" name="atras5" id="atras5" value="Atras" class="cancelar"></td>
                                            <td><input type="button" name="siguiente5" id="siguiente5" value="Siguiente" class="siguiente"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 1em;"></td>
                                <td style="font-size: 1vw !important; max-width: 100%;">¿Ya tienes una cuenta? <a href="inicio_sesion.php">Inicia Sesión</a></td>
                            </tr>
                        </table>

                        <!--Tabla de registro invitado-->
                        <table class="tabla-registro" id="tabla-registro6" style="display: none">
                            <colgroup>
                                <col class="columna-izquierda">
                                <col class="columna-derecha">
                            </colgroup>
                            <tr class="indicaciones">
                                <td colspan="2">Id UPAEP de quien te invitó</td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 1em;"><input required type="text" name="idupaepinvitado" id="idupaepinvitado" placeholder="ID Invitado" class="entrada1"></td>
                            </tr>
                            <tr>
                                <td colspan="2">Nombre de la institución educativa a la que perteneces</td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 1em;"><input required type="text" name="institucion" id="institucion" placeholder="Institución" class="entrada_lic"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <table width="100%">
                                        <tr>
                                            <td width="40%"><input type="button" name="atras6" id="atras6" value="Atras" class="cancelar"></td>
                                            <td><input type="button" name="siguiente6" id="siguiente6" value="Siguiente" class="siguiente"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 1em;"></td>
                                <td style="font-size: 1vw !important; max-width: 100%;">¿Ya tienes una cuenta? <a href="inicio_sesion.php">Inicia Sesión</a></td>
                            </tr>
                        </table>

                        <!--Tabla de registro 7-->
                        <table class="tabla-registro" id="tabla-registro7" style="display: none">
                            <colgroup>
                                <col class="columna-izquierda">
                                <col class="columna-derecha">
                            </colgroup>
                            <tr class="indicaciones">
                                <td>*Correo electronico</td>
                                <td>*Número telefónico</td>
                            </tr>
                            <tr>
                                <td><input required type="text" name="correo" id="correo" placeholder="Correo electronico" class="entrada1"></td>
                                <td><input required type="number" name="numero_tel" id="numero_tel" placeholder="Número telefónico" class="entrada1"></td>
                            </tr>
                            <tr class="indicaciones">
                                <td>*Contraseña</td>
                                <td>*Confirmar contraseña</td>
                            </tr>
                            <tr class="indicaciones">
                                <td><input required type="text" name="contrasena" id="contrasena" placeholder="Contraseña" class="entrada1"></td>
                                <td><input required type="text" name="conf_contrasena" id="conf_contrasena" placeholder="Confirmar Contraseña" class="entrada1"></td>
                            </tr>
                            <tr>
                                <td>
                                    <table>
                                        <td><br><input required type="checkbox" name="terminos_condiciones" id="terminos_condiciones" style="transform: scale(2);"></td>
                                        <td class="invitado" style="padding-left: 5px;"><br>Acepto los <b>Terminos y Condiciones</b>
                                        y<br> autorizo el uso de mis datos de acuerdo
                                        a la <b>Declaración de la Privacidad</b>.</td>
                                    </table>     
                                </td>  
                                <td>
                                    <table width="80%">
                                        <tr class="botones">
                                            <td width="40%"><input type="button" name="atras7" id="atras7" value="Atras" class="cancelar"></td>
                                            <td><input type="submit" name="registrar" id="registrar" value="Registrar" class="siguiente"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 1em;"></td>
                                <td style="font-size: 1vw !important; max-width: 100%;">¿Ya tienes una cuenta? <a href="inicio_sesion.php">Inicia Sesión</a></td>
                            </tr>
                        </table>
                    </form>

                </div>
            </div>
        
    </main>

    <footer>
        <?php 
        include '../includes/footer.php';
        ?>
    </footer>

    <script src="../assets/js/registro.js"></script>
</body>
</html>