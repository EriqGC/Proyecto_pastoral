export function initDialog(name){
    const dialogElement = document.querySelector(`[data-dialog="${name}"]`);
    const closeButtonElements = document.querySelectorAll("[data-dialog-close-button]");

    function close(){
        dialogElement.close();
    }

    for(const closeButtonElement of closeButtonElements){
        closeButtonElement.addEventListener('click', () =>{
            close();
        });
    }

    dialogElement.addEventListener('click', (event) =>{
        if(event.target === dialogElement){
            close();
        }
    });

    return{
        dialogElement,
        open(){
            dialogElement.showModal();
        },
        close(){
            close();
        }
    };
}