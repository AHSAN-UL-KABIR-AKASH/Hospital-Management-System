// Get the settings icon and dropdown
const settingsIcon = document.getElementById("settings-icon");
const settingsDropdown = document.getElementById("settings-dropdown");

// Toggle dropdown visibility on click
settingsIcon.addEventListener("click", function (e) {
    e.preventDefault(); // Prevent default anchor behavior
    if (settingsDropdown.style.display === "block") {
        settingsDropdown.style.display = "none";
    } else {
        settingsDropdown.style.display = "block";
    }
});

// Close dropdown when clicking outside
document.addEventListener("click", function (event) {
    if (!settingsIcon.contains(event.target) && !settingsDropdown.contains(event.target)) {
        settingsDropdown.style.display = "none";
    }
});

$(document).ready(function () {
    // Autofill patient details
    $('#phone').blur(function () {
        const phone = $(this).val();
        if (phone) {
            $.ajax({
                url: '/Comilla_central_medical//Admission/get_patient.php',
                type: 'POST',
                data: { phone: phone },
                dataType: 'json',
                success: function (data) {
                    if (data.exists) {
                        $('#patient-id').val(data.PatientID);
                        $('#fullname').val(data.PatientName);
                        $('#dob').val(data.DateOfBirth);
                        $('#gender').val(data.Gender);
                        $('#email').val(data.Email);
                    } 
                    else {
                        $('#patient-id').val('');
                       
                    }
                }
            });
        }
    });

    // Load doctors based on specialty
    $('#specialty').change(function () {
        const specialty = $(this).val();
        if (specialty) {
            $.ajax({
                url: '/Comilla_central_medical//Admission/get_doctors.php',
                type: 'POST',
                data: { specialty: specialty },
                success: function (response) {
                    $('#doctor').html(response);
                }
            });
        } else {
            $('#doctor').html('<option value="">Select the Specialty First</option>');
        }
    });
});