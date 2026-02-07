import {initDialog} from "./dialog.js";
import {procesarFormato} from "./procesarFormato.js";

export function eventoFormato(){

    const dialog = initDialog("event-form");
    const eventForm = procesarFormato();

    document.addEventListener("event-create-request", () =>{
        dialog.open();
    });

    dialog.dialogElement.addEventListener("close", ()=>{
        eventForm.reset();
    });

    eventForm.formElement.addEventListener("event-create", () =>{
        dialog.close();
    });
}