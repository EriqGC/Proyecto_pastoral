import { isTheSameDay } from "./date.js";

const events = [];

export function initEventStore() {
  document.addEventListener("event-create", (event) => {
    const createdEvent = event.detail.event;

    events.push(createdEvent);

    document.dispatchEvent(new CustomEvent("events-change", {
      bubbles: true
    }));
  });

  return{
    getEventsByDate(date){
      return events.filter((event) =>{
        const eventDate = new Date(event.date);
        return isTheSameDay(eventDate, date);
      });
    }
  };
}
