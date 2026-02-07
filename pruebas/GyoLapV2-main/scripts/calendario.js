import { today } from "./date.js";
import { initWeekCalendar } from "./week-calendar.js";


let selectedDate = today();

export function initCalendar(eventStore) {
  const calendarElement = document.querySelector(".calendar");
  const contentElement = calendarElement.querySelector("[data-calendar-content]");

  function refreshCalendar() {
    const calendarScrollableElement = calendarElement.querySelector("[data-calendar-scrollable]");

    const scrollTop = calendarScrollableElement === null ? 0 : calendarScrollableElement.scrollTop;

    contentElement.replaceChildren();

    initWeekCalendar(contentElement, selectedDate, eventStore);

    calendarElement.querySelector("[data-calendar-scrollable]").scrollTo({ top: scrollTop });
  }

  document.addEventListener("date-change", (event) => {
    selectedDate = event.detail.date;
    refreshCalendar();
  });

  document.addEventListener("events-change", () => {
    refreshCalendar();
  });


  refreshCalendar();
}
