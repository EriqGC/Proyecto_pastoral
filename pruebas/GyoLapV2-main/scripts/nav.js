import { today, addDays, subtractDays } from "./date.js";

const dataFormatter = new Intl.DateTimeFormat("es-MX", {
    month: "long",
    year: "numeric"
});

export function initNav() {
    const todayButtonElements = document.querySelectorAll("[data-nav-today-button]");

    const previousButtonElement = document.querySelector("[data-nav-previous-button]");

    const nextButtonElement = document.querySelector("[data-nav-next-button]");

    const dateElement = document.querySelector("[data-nav-date]");

    let selectedDate = today();

    for (const todayButtonElement of todayButtonElements) {
        todayButtonElement.addEventListener("click", () => {
            todayButtonElement.dispatchEvent(new CustomEvent("date-change", {
                detail: {
                    date: today()
                },
                bubbles: true
            }));
        });
    }

    previousButtonElement.addEventListener("click", () => {
        previousButtonElement.dispatchEvent(new CustomEvent("date-change", {
            detail: {
                date: getPreviousDate(selectedDate)
            },
            bubbles: true
        }));
    });

    nextButtonElement.addEventListener("click", () => {
        nextButtonElement.dispatchEvent(new CustomEvent("date-change", {
            detail: {
                date: getNextDate(selectedDate)
            },
            bubbles: true
        }));
    });

    document.addEventListener("date-change", (event) => {
        selectedDate = event.detail.date;
        refreshDateElement(dateElement, selectedDate);
      });      

    refreshDateElement(dateElement, selectedDate);
}

function refreshDateElement(dateElement, selectedDate) {
    dateElement.textContent = dataFormatter.format(selectedDate);
}

function getPreviousDate(selectedDate) {
    return subtractDays(selectedDate, 7);
}

function getNextDate(selectedDate){
    return addDays(selectedDate, 7);
}