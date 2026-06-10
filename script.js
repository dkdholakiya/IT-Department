document.addEventListener("DOMContentLoaded", function () {

    // Get references to the dropdown and the dynamic sections
    const reportTypeSelect = document.getElementById('reportType');

    const logisticsSection = document.getElementById('logisticsSection');
    const guestSection = document.getElementById('guestSection');
    const achievementSection = document.getElementById('achievementSection');
    const visitSection = document.getElementById('visitSection');

    // Function to hide all dynamic sections
    function hideAllSections() {
        logisticsSection.classList.add('hidden');
        guestSection.classList.add('hidden');
        achievementSection.classList.add('hidden');
        visitSection.classList.add('hidden');
    }

    // Event Listener for Dropdown Change
    reportTypeSelect.addEventListener('change', function () {
        // First, hide everything
        hideAllSections();

        // Get the selected value
        const selectedValue = this.value;

        // Show specific sections based on the selection
        if (selectedValue === 'academic') {
            logisticsSection.classList.remove('hidden');
        }
        else if (selectedValue === 'guest') {
            logisticsSection.classList.remove('hidden');
            guestSection.classList.remove('hidden');
        }
        else if (selectedValue === 'achievement') {
            achievementSection.classList.remove('hidden');
            // Logistics are usually omitted for external achievements
        }
        else if (selectedValue === 'visit') {
            visitSection.classList.remove('hidden');
            // Logistics omitted as it's off-campus
        }
    });
});
