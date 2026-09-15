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

// Handle form submission
document.querySelector('.appointment-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    fetch('p_update.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === 'success') {
                // Show success message
                alert(data.message); // Use a modal or notification for better UX
            } else {
                // Show error message
                alert(data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        });
});
