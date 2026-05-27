const form = document.querySelector(".contact-form");
const alertBox = document.querySelector(".js-form-alert");

if(form) {
    form.addEventListener("submit", function (e) {
        e.preventDefault();
    
        if( alertBox ) {
            alertBox.style.display = "block";
        }
    });
}