// Dark and Light Mode with localStorage
(function () {
    let darkMode = localStorage.getItem("darkMode");
    const darkSwitch = document.getElementById("switch");

    // Enable and Disable Darkmode
    const enableDarkMode = () => {
        document.body.classList.add("darkmode");
        localStorage.setItem("darkMode", "enabled");
    };

    const disableDarkMode = () => {
        document.body.classList.remove("darkmode");
        localStorage.setItem("darkMode", null);
    };

    // User Already Enable Darkmode
    if (darkMode === "enabled") {
        enableDarkMode();
    }

    // User Clicks the Darkmode Toggle
    darkSwitch.addEventListener("click", () => {
        darkMode = localStorage.getItem("darkMode");
        if (darkMode !== "enabled") {
            enableDarkMode();
        } else {
            disableDarkMode();
        }
    });
})();