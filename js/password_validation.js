document.addEventListener('DOMContentLoaded', (event) => {
    
    console.log("Loaded");
    var password=document.getElementById("password");
    
    var password_confirmation=document.getElementById("passwordConfirmation");
    if (password!=password_confirmation){
        alert("passwords don't match");
        

    }
  })