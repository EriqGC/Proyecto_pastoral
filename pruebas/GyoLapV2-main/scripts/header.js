// scripts/header.js
(async () => {
    const contenedor = document.querySelector('#header');
    if (!contenedor) return;
  
    // Cargar el contenido HTML del header
    const res = await fetch("../components/header.html");
    const html = await res.text();
    contenedor.innerHTML = html;
  
    // Cargar CSS del header si no está presente
    const cssId = "header-css";
    if (!document.querySelector(cssId)) {
      const link = document.createElement("link");
      link.id = cssId;
      link.rel = "stylesheet";
      link.href = "../styles/header.css";
      document.head.appendChild(link);
    }
  })();
  