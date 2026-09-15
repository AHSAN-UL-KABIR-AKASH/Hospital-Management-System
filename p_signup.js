// Real-time validation for the name, contact number, email, and password fields
document.addEventListener("DOMContentLoaded", function()  {
    const form = document.querySelector("form");
    const nameField = document.getElementById("full_name");
    const contactField = document.getElementById("contact_number");
    const emailField = document.getElementById("email");
    const passwordField = document.getElementById("password");

    const nameError = document.getElementById("name_error");
    const contactError = document.getElementById("contact_error");
    const emailError = document.getElementById("email_error");
    const passwordError = document.getElementById("password_error");

    // Name validation: Only letters and spaces
    nameField.addEventListener("input", function() {
    const nameValue = nameField.value.trim();
    
    // Regular expression to allow 'Md', first and middle names, and optional last name
    const namePattern = /^([A-Za-z]+(\s[A-Za-z]+)*)$/;

    if (namePattern.test(nameValue)) {
        // Valid name format
        nameError.textContent = "";
        nameField.style.borderColor = "green";
    } else {
        // Invalid name format
        nameError.textContent = "Invalid Naming Format. Correct format: Md Jahidul Haque Fahad";
        nameError.style.color = "red";
        nameField.style.borderColor = "red";
    }
});


    // Contact number validation: 11 digits
    contactField.addEventListener("input", function () {
        const contactNumber = contactField.value.trim();
    
        // Validate the contact number (11-digit validation)
        if (/^\d{11}$/.test(contactNumber)) {
            // Make AJAX request to validate the contact number in the database
            fetch("p_signupData.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `contact_number=${contactNumber}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.exists) {
                        contactError.textContent = data.message; // Show error message
                        contactError.style.color = "red";
                        contactField.style.borderColor = "red";
                    } else {
                        contactError.textContent = data.message; // Show success message
                        contactError.style.color = "green";
                        contactField.style.borderColor = "green";
                    }
                })
                .catch((error) => console.error("Error:", error));
        } else {
            // Invalid contact number format
            contactError.textContent = "Please enter a valid 11-digit contact number.";
            contactError.style.color = "red";
            contactField.style.borderColor = "red";
        }
    });
    

    // Email validation: Basic email format
    emailField.addEventListener("input", function()  {
        const emailValue = emailField.value.trim();
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (!emailPattern.test(emailValue)) {
            emailError.textContent = "Please enter a valid email address.";
            emailError.style.color = "red";
            emailField.style.borderColor = "red";
        } else {
            emailError.textContent = "";
            emailField.style.borderColor = "green";
        }
    });

    // Password validation: Minimum 8 characters
    passwordField.addEventListener("input", function()  {
        const passwordValue = passwordField.value.trim();
        if (passwordValue.length < 8) {
            passwordError.textContent = "Password must be at least 8 characters long.";
            passwordError.style.color = "red";
            passwordField.style.borderColor = "red";
        } else {
            passwordError.textContent = "";
            passwordField.style.borderColor = "green";
        }
    });

    // Form submission validation
    form.addEventListener("submit", function(e) {
        // Prevent the default form submission
        e.preventDefault();
    
        // Validate fields before submitting
        const nameValid = /^([A-Za-z]+(\s[A-Za-z]+)*)$/.test(nameField.value.trim());
        const contactValid = /^\d{11}$/.test(contactField.value.trim());
        const emailValid = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(emailField.value.trim());
        const passwordValid = passwordField.value.trim().length >= 8;
    
        if (nameValid && contactValid && emailValid && passwordValid) {
            const formData = new FormData(form);
            
            // Send the form data to the server
            fetch('p_signupData.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message); // Success message
                    window.location.href = "loginPage.php"; 
                   
                } else {
                    alert(data.message); // Display error message
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred during signup.");
            });
        } else {
            alert("Please make sure all fields are filled out correctly.");
        }
    });
});
