/* Cerrar sesion */
function abrirCerrarsesion() {
    const ventana = document.querySelector("#cerrarsesion");
    if (ventana) {
      ventana.style.display = "flex";
    }
  }
  
  function cerrarCerrarsesion() {
    const ventana = document.querySelector("#cerrarsesion");
    if (ventana) {
      ventana.style.display = "none";
    }
  }
  
  function confirmarSalir() {
    window.location.href = "login.html"; // AQUI VA LA LANDING
  }
  
  const btnSalir = document.querySelector("#btnSalir");
  const btnCancelar = document.querySelector("#btn-cancelar");
  
  if (btnSalir) {
    btnSalir.addEventListener("click", confirmarSalir);
  }
  
  if (btnCancelar) {
    btnCancelar.addEventListener("click", cerrarCerrarsesion);
  }