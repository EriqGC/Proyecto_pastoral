import {validateEvent} from "./event.js";

export function procesarFormato(){
    const formElement = document.querySelector("[data-event-form]");

    formElement.addEventListener('submit', (event)=>{
        event.preventDefault();

        const formEvent = formIntoEvent(formElement);
        const validationError = validateEvent(formEvent);
        if(validationError !== null){
            alert(validationError);
            return;
        }
       
        formElement.dispatchEvent(new CustomEvent("event-create", {
            detail: {
                event: formEvent
            },
            bubbles: true
        }));
    });

    return{
        formElement,
        reset(){
            formElement.reset();
        }
    };
}

function formIntoEvent(formElement) {
    const formData = new FormData(formElement);
    const title = formData.get("title");
    const date = formData.get("date");
    const start = formData.get("start-time");
    const end = formData.get("end-time");

    function convertirHoraAMinutos(horaStr) {
        const [hora, minutos] = horaStr.split(":").map(Number);
        return hora * 60 + minutos;
      }
  
    const event = {
        title,
        date: new Date(date),
        startTime: convertirHoraAMinutos(start),
        endTime: convertirHoraAMinutos(end)
    };
    return event;
  }