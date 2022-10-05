document.addEventListener ('DOMContentLoaded',() => {
    let popupBG = document.querySelector("#buy-dialog");
    let popup = document.querySelector ('.popup');
    const openPopupButtons = document.querySelector("#buy-button");
    let closePopupButton = document.querySelector('.close__popup')
    
    openPopupButtons.addEventListener("click", (e) => {
      e.preventDefault();
      
      popupBG.classList.add("active");
      popup.classList.add("active");
    });   

    closePopupButton.addEventListener("click", () => {
      
      ;
      popupBG.classList.remove("active");
      popup.classList.remove("active");
    });   

    document.addEventListener('click', (e) => {
      if (e.target === popupBG){

        popupBG.classList.remove("active");
        popup.classList.remove("active");
      }
    });



    let backgroundIntro = document.querySelector("#intro_head");
    let flag = true;

    backgroundIntro.addEventListener("click", (e) =>{

        e.preventDefault();

        if(flag){
            flag = false;
            backgroundIntro.classList.add("gif");

            setTimeout(() => { flag = true; }, 1000)
            setTimeout(() => {
                backgroundIntro.classList.remove("gif");
            }, 650);



        }

    });








})
