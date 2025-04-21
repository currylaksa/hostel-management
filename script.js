// script.js
function validateForm() {
    const user = document.getElementById("username").value.trim();
    const pass = document.getElementById("password").value.trim();
    const role = document.getElementById("role").value.trim();
    
    if (user === "" || pass === "" || role === "") {
        alert("Please fill in all fields.");
        return false;
    }
    return true;
}

// Check for error parameter in URL
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        document.getElementById('error-message').innerHTML = "Invalid username or password";
    }
}
  