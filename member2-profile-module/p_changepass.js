 // Dropdown logic
 const settingsIcon = document.getElementById("settings-icon");
 const settingsDropdown = document.getElementById("settings-dropdown");

 settingsIcon.addEventListener("click", function (e) {
     e.preventDefault();
     settingsDropdown.style.display =
         settingsDropdown.style.display === "block" ? "none" : "block";
 });

 document.addEventListener("click", function (event) {
     if (!settingsIcon.contains(event.target) && !settingsDropdown.contains(event.target)) {
         settingsDropdown.style.display = "none";
     }
 });

 // Password validation logic
 document.addEventListener("DOMContentLoaded", function () {
     const passwordField = document.getElementById("new_password");
     const passwordError = document.getElementById("password_error");
     const form = document.getElementById("passwordForm");

     // Password validation: Minimum 8 characters
     passwordField.addEventListener("input", function () {
         const passwordValue = passwordField.value.trim();
         if (passwordValue.length < 8) {
             passwordError.textContent = "Password must be at least 8 characters long.";
             passwordField.style.borderColor = "red";
         } else {
             passwordError.textContent = "";
             passwordField.style.borderColor = "green";
         }
     });

     // Form submission validation
     form.addEventListener("submit", function (e) {
         const passwordValid = passwordField.value.trim().length >= 8;

         if (!passwordValid) {
             e.preventDefault(); // Prevent form submission
             passwordError.textContent = "Password must be at least 8 characters long.";
             passwordField.style.borderColor = "red";
         } else {
             passwordError.textContent = "";
             passwordField.style.borderColor = "green";

             // Optional: Simulate server submission
             e.preventDefault(); // Remove this if server-side submission is needed
             alert("Form submitted successfully!");
         }
     });
 });