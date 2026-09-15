function fetchPatientData() {
    // Get the search inputs
    const patientId = document.getElementById('patient-id').value;
    const patientName = document.getElementById('patient-name').value;
    const patientMobile = document.getElementById('patient-mobile').value;

    // Prepare the AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_patient.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Update the table body with the response
            document.getElementById('patient-data').innerHTML = xhr.responseText;
        }
    };

    // Send the request with search parameters
    const params = `patientId=${encodeURIComponent(patientId)}&patientName=${encodeURIComponent(patientName)}&patientMobile=${encodeURIComponent(patientMobile)}`;
    xhr.send(params);
}

function dischargePatient(patientId) {
    if (confirm("Are you sure you want to discharge this patient?")) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_discharge.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText);
                fetchPatientData(); // Refresh the table
            }
        };

        xhr.send(`patientId=${encodeURIComponent(patientId)}`);
    }
}
