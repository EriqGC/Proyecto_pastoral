import { initCalendar } from "./calendario.js";
import { configurarBotonCrearCita } from "./crearcita.js";
import { eventoFormato } from "./formatoEvento.js";
import { initEventStore } from "./event-store.js";
import { initNav } from "./nav.js";

document.addEventListener("DOMContentLoaded", () => {
    const eventStore = initEventStore();
    initCalendar(eventStore);
    configurarBotonCrearCita();
    eventoFormato();
    initNav();
  });
  