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