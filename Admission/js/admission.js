
$(document).ready(function () {
    // Autofill patient details
    $('#phone').blur(function () {
        const phone = $(this).val();
        if (phone) {
            $.ajax({
                url: 'get_patient.php',
                type: 'POST',
                data: { phone: phone },
                dataType: 'json',
                success: function (data) {
                    if (data.exists) {
                        $('#patient-id').val(data.PatientID);
                        $('#name').val(data.PatientName);
                        $('#dob').val(data.DateOfBirth);
                        $('#gender').val(data.Gender);
                        $('#email').val(data.Email);
                        $('#blood-group').val(data.BloodGroup);
                        $('#address').val(data.Address);
                    } else {
                        $('#patient-id').val('');
                        $('.admission-form').trigger('reset');
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
                url: 'get_doctors.php',
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