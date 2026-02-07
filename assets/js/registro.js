/*registro.js*/

document.getElementById('cancelar').addEventListener('click', function() {
    window.location.href = "../index.php";
});

document.getElementById('atras2').addEventListener('click', function() {
    document.getElementById('tabla-registro2').style.display = 'none';
    document.getElementById('tabla-registro1').style.display = 'block';
});

document.getElementById('atras3').addEventListener('click', function() {
    document.getElementById('tabla-registro3').style.display = 'none';
    document.getElementById('tabla-registro2').style.display = 'block';
});

document.getElementById('atras4').addEventListener('click', function() {
    document.getElementById('tabla-registro4').style.display = 'none';
    document.getElementById('tabla-registro3').style.display = 'block';
});

document.getElementById('atras5').addEventListener('click', function() {
    document.getElementById('tabla-registro5').style.display = 'none';
    document.getElementById('tabla-registro4').style.display = 'block';
});

document.getElementById('atras6').addEventListener('click', function() {
    document.getElementById('tabla-registro6').style.display = 'none';
    document.getElementById('tabla-registro4').style.display = 'block';
});

document.getElementById('siguiente6').addEventListener('click', function() {
    document.getElementById('tabla-registro6').style.display = 'none';
    document.getElementById('tabla-registro7').style.display = 'block';
});     

document.getElementById('atras7').addEventListener('click', function() {
    document.getElementById('tabla-registro7').style.display = 'none';
    document.getElementById('tabla-registro4').style.display = 'block';
});




function valida_envio1(e){
    e.preventDefault();

    if(!document.form.nombre_alumno.value || !document.form.apellido_p.value || !document.form.apellido_m.value
        || !document.form.sexo.value 
    ){
        alert("Campo Faltante");
        return false;
    }


    const dia = parseInt(document.getElementById('dia_n').value, 10);
    const mes = parseInt(document.getElementById('mes_n').value, 10) - 1;
    const año = parseInt(document.getElementById('anio_n').value, 10);

    const hoy = new Date();
    const fechaNacimiento = new Date(año, mes, dia);
    if (fechaNacimiento.getDate() !== dia || 
        fechaNacimiento.getMonth() !== mes || 
        fechaNacimiento.getFullYear() !== año) {
        alert("Por favor ingresa una fecha válida");
        return false;
    }
    
    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    const mesActual = hoy.getMonth();
    const diaActual = hoy.getDate();

    if (mes > mesActual || (mes === mesActual && dia > diaActual)) {
        edad--;
    }

    if (edad < 18) {
        alert("Debes tener al menos 18 años");
        return false;
    } else if (edad > 100) {
        alert("La edad ingresada no es válida");
        return false;
    }
    
    document.getElementById('tabla-registro1').style.display = 'none';
    document.getElementById('tabla-registro2').style.display = 'block';
    return true;
}

document.getElementById("siguiente1").addEventListener("click", valida_envio1);

function valida_envio2(e){

    if(!document.form.numero_emergencias.value){
        alert("Escribe un número de emergencias");
        return false;
    }

    let numeroEmergencia = document.getElementById('numero_emergencias').value;
    if(numeroEmergencia.length != 10){
        alert("Escribe un número válido");
        document.getElementById('numero_emergencias').focus();
        return false;
    }

    document.getElementById('tabla-registro2').style.display = 'none';
    document.getElementById('tabla-registro3').style.display = 'block';
    return true;
}

document.getElementById("siguiente2").addEventListener("click", valida_envio2);


function valida_envio3(e){
    e.preventDefault(); 

    const checkboxes = document.querySelectorAll('input[name="habilidades[]"]');            
    const algunoMarcado = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!algunoMarcado) {
        alert("Debes seleccionar al menos una habilidad");
        return false;
    }
    if(document.form.playera.selectedIndex == 0){
        alert("Debe seleccionar una talla de playera");
        document.form.playera.focus()
        return false;
    }

    document.getElementById('tabla-registro3').style.display = 'none';
    document.getElementById('tabla-registro4').style.display = 'block';
    return true;          
}

document.getElementById("siguiente3").addEventListener("click", valida_envio3);


function valida_envio4(e){
    e.preventDefault(); 

    if(!document.form.residencia_o.value || !document.form.residencia_a.value){
        alert("Campo Faltante");
        return false;
    }

    if(document.form.mensaje.selectedIndex == 0){
        alert("Elige un mensaje");
        document.form.mensaje.focus();
        return false;
    }

    if(!document.form.invitado.value){
        alert("Elige un mensaje");
        document.form.mensaje.focus();
        return false;
    }

    const invitado = document.form.invitado.value;

    if(invitado == "estudiante"){
        document.getElementById('tabla-registro4').style.display = 'none';
        document.getElementById('tabla-registro5').style.display = 'block';
        return true;  
    }
    
    document.getElementById('tabla-registro4').style.display = 'none';
    document.getElementById('tabla-registro6').style.display = 'block';
    return true;         
}

document.getElementById("siguiente4").addEventListener("click", valida_envio4);


function valida_envio5(e){
    e.preventDefault(); 
 
    if(!document.form.idupaep.value || !document.form.matricula.value || !document.form.licenciatura.value){
        alert("Campo Faltante");
        return false;
    }

    
    document.getElementById('tabla-registro5').style.display = 'none';
    document.getElementById('tabla-registro7').style.display = 'block';
    return true;
}

document.getElementById("siguiente5").addEventListener("click", valida_envio5);


function valida_envio7(e){
    e.preventDefault(); 

    const correo = document.getElementById('correo').value;
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!regex.test(correo)) {
        alert("Ingresa un correo valido");
        e.preventDefault(); 
        return false;
    }

    const num = document.getElementById('numero_tel').value;

    if(num.length != 10){
        alert("Escribe un número válido");
        document.getElementById('numero_tel').focus();
        return false;
    }

    const contrasena = document.form.contrasena.value;
    const conf_contrasena = document.form.conf_contrasena.value;

    if(contrasena != conf_contrasena){
        alert("Las contraseñas no coinciden");
        return false;
    }

    document.form.submit();
    return true;
}

document.getElementById("registrar").addEventListener("click", valida_envio7);