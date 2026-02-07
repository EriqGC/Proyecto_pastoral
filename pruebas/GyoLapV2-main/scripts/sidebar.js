(async () => {
    const contenedor = document.querySelector('#sidebar');
    if (!contenedor) return;
  
    // Cargar el contenido HTML
    const res = await fetch("../components/sidebar.html");
    const html = await res.text();
    contenedor.innerHTML = `<div class="sidebar">${html}</div>`;
  
    // Cargar CSS del sidebar si no está presente
    const cssId = "sidebar-css";
    if (!document.querySelector(cssId)) {
      const link = document.createElement("link");
      link.id = cssId;
      link.rel = "stylesheet";
      link.href = "../styles/sidebar.css";
      document.head.appendChild(link);
    }
  
    // Marcar elemento activo
    const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll(".menu-item").forEach(link => {
      if (link.href && link.href.includes(currentPage)) {
        link.classList.add("active");
      }
    });
  })();
  