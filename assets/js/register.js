document.addEventListener("DOMContentLoaded", function () {
    const passwordField = document.getElementById("password");
    const confirmPasswordField = document.getElementById("confirm_password");
    const submitButton = document.getElementById("submit-btn");
    const passwordHint = document.getElementById("password-rules");
    const passwordMatch = document.getElementById("password-match");
    
    function checkPasswords() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;
        const isValid = password.length >= 8 && /[A-Z]/.test(password) && /\d/.test(password);
    
        passwordHint.style.color = isValid ? "green" : "red";
        confirmPasswordField.style.borderColor = password === confirmPassword ? "green" : "red";
    
        if (!isValid || password !== confirmPassword) {
            submitButton.disabled = true;
        } else {
            submitButton.disabled = false;
        }
    }
    

    passwordField.addEventListener("input", checkPasswords);
    confirmPasswordField.addEventListener("input", checkPasswords);
});
