import { today } from "./date.js"; // o usa tu propia función si ya tienes una
let selectedDate = today(); // Valor inicial

export function configurarBotonCrearCita() {
  const buttonElements = document.querySelectorAll("[data-event-create-button]");

  for (const buttonElement of buttonElements) {
    buttonElement.addEventListener("click", () => {
      buttonElement.dispatchEvent(new CustomEvent("event-create-request", {
        detail: {
          date: selectedDate,
        },
        bubbles: true
      }));
    });
  }

  document.addEventListener("date-change", (event) => {
    selectedDate = event.detail.date;
  });
}
