document.addEventListener("DOMContentLoaded", function() {
    const userTypeSelect = document.getElementById('user-type');
    const usernameGroup = document.getElementById('username-group');
    const contactNumberGroup = document.getElementById('contact-group');
    const signupLink = document.getElementById('signup-link');
    const loginForm = document.getElementById("login-form");

    // Update the visibility of fields based on user type
    userTypeSelect.addEventListener('change', function() {
        if (userTypeSelect.value === 'patient') {
            usernameGroup.style.display = 'none'; // Hide Username field
            contactNumberGroup.style.display = 'block'; // Show Contact Number field
            signupLink.style.display = 'block'; // Show signup link for patients only
        } else {
            usernameGroup.style.display = 'block'; // Show Username field
            contactNumberGroup.style.display = 'none'; // Hide Contact Number field
            signupLink.style.display = 'none'; // Hide signup link for non-patients
        }
    });


    loginForm.addEventListener("submit", function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Collect form data
        const formData = new FormData(loginForm);

        // Perform basic client-side validation (optional)
        const userType = userTypeSelect.value;
        if (userType === 'patient') {
            const contactNumber = formData.get('contact_number'); // Adjust the name if needed
            if (!contactNumber || contactNumber.trim() === '') {
                alert('Please provide your contact number.');
                return;
            }
        } else {
            const username = formData.get('username'); // Adjust the name if needed
            if (!username || username.trim() === '') {
                alert('Please provide your username.');
                return;
            }
        }

        // Send AJAX request with form data to the server
        fetch("loginValidation.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json()) // Parse JSON response
        .then(data => {
            if (data.success) {
                alert(data.message); // Display success message
                window.location.href = data.redirect_url; // Redirect to the dashboard
            } 
            else {
                alert(data.message); // Display error message
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred during login.");
        });
    });
});
