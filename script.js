document.addEventListener("DOMContentLoaded", function () {

    // facultyData is loaded globally from facultyData.js

    // ── Search Dropdown Logic ──
    const facultySearch = document.getElementById("facultySearch");
    const preparedBy = document.getElementById("preparedBy");
    const facultyDropdownList = document.getElementById("facultyDropdownList");
    const facultyError = document.getElementById("facultyError");

    const facultyDesignation = document.getElementById("facultyDesignation");
    const facultyEmpId = document.getElementById("facultyEmpId");
    const facultyEmail = document.getElementById("facultyEmail");
    const facultyPhone = document.getElementById("facultyPhone");

    // Initialize list with all faculty members
    renderDropdown(facultyData);

    // Toggle dropdown visibility on click/focus
    facultySearch.addEventListener("focus", function () {
        facultyDropdownList.classList.add("show");
        filterFaculty();
    });

    facultySearch.addEventListener("input", function () {
        facultyDropdownList.classList.add("show");
        filterFaculty();
        
        // If they clear the search input, reset selection
        if (this.value.trim() === "") {
            clearSelection();
        }
    });

    // Close dropdown on clicking outside
    document.addEventListener("click", function (e) {
        if (!e.target.closest(".search-select-wrap")) {
            facultyDropdownList.classList.remove("show");
            // If they clicked away without choosing and input matches nothing, reset
            validateSearchInput();
        }
    });

    function filterFaculty() {
        const query = facultySearch.value.toLowerCase().replace("prof.", "").trim();
        const filtered = facultyData.filter(member => 
            member.name.toLowerCase().includes(query) ||
            member.designation.toLowerCase().includes(query) ||
            member.empId.toLowerCase().includes(query)
        );
        renderDropdown(filtered);
    }

    function renderDropdown(list) {
        facultyDropdownList.innerHTML = "";
        if (list.length === 0) {
            facultyDropdownList.innerHTML = `<div class="no-results-item">No faculty members found</div>`;
            return;
        }

        list.forEach(member => {
            const item = document.createElement("div");
            item.className = "dropdown-item";
            item.innerHTML = `
                <div class="item-avatar ${member.avatarClass}">${member.initials}</div>
                <div class="item-info">
                    <div class="item-name">${member.name}</div>
                    <div class="item-desg">${member.designation} &nbsp;·&nbsp; ${member.empId}</div>
                </div>
            `;
            item.addEventListener("click", function () {
                selectFaculty(member);
            });
            facultyDropdownList.appendChild(item);
        });
    }

    function selectFaculty(member) {
        facultySearch.value = member.name;
        preparedBy.value = member.name;
        
        facultyDesignation.value = member.designation;
        facultyEmpId.value = member.empId;
        facultyEmail.value = member.email;
        facultyPhone.value = "+91 " + member.phone;

        facultySearch.classList.remove("input-error");
        facultyError.classList.add("hidden");

        facultyDropdownList.classList.remove("show");
    }

    function clearSelection() {
        preparedBy.value = "";
        facultyDesignation.value = "";
        facultyEmpId.value = "";
        facultyEmail.value = "";
        facultyPhone.value = "";
    }

    function validateSearchInput() {
        const val = facultySearch.value.trim();
        if (val === "") {
            clearSelection();
            return;
        }
        
        // Check if there is an exact matching faculty name
        const match = facultyData.find(m => m.name.toLowerCase() === val.toLowerCase());
        if (match) {
            selectFaculty(match);
        } else {
            // Check if there's a partial match that is unique
            const partialMatches = facultyData.filter(m => m.name.toLowerCase().includes(val.toLowerCase()));
            if (partialMatches.length === 1) {
                selectFaculty(partialMatches[0]);
            } else {
                // Reset since the input is invalid
                clearSelection();
                facultySearch.value = "";
            }
        }
    }

    // ── Multi-Step Form Navigation ──
    const step1 = document.getElementById("step1");
    const step2 = document.getElementById("step2");
    const stepIndicator1 = document.getElementById("stepIndicator1");
    const stepIndicator2 = document.getElementById("stepIndicator2");

    const nextStepBtn = document.getElementById("nextStepBtn");
    const prevStepBtn = document.getElementById("prevStepBtn");

    nextStepBtn.addEventListener("click", function () {
        // Validate Step 1 details
        validateSearchInput();
        if (!preparedBy.value) {
            facultySearch.classList.add("input-error");
            facultyError.classList.remove("hidden");
            facultySearch.focus();
            return;
        }

        // Smooth transition to Step 2
        step1.classList.remove("active");
        step1.classList.add("hidden");
        
        step2.classList.remove("hidden");
        step2.classList.add("active");

        stepIndicator1.classList.add("completed");
        stepIndicator2.classList.add("active");
        
        // Trigger reportType select check in case it's pre-filled
        reportTypeSelect.dispatchEvent(new Event("change"));
    });

    prevStepBtn.addEventListener("click", function () {
        // Smooth transition back to Step 1
        step2.classList.remove("active");
        step2.classList.add("hidden");

        step1.classList.remove("hidden");
        step1.classList.add("active");

        stepIndicator1.classList.remove("completed");
        stepIndicator2.classList.remove("active");
    });


    // ── Dynamic Event Logistics ──
    const reportTypeSelect = document.getElementById('reportType');
    const logisticsSection = document.getElementById('logisticsSection');
    const guestSection = document.getElementById('guestSection');
    const achievementSection = document.getElementById('achievementSection');
    const visitSection = document.getElementById('visitSection');

    function hideAllSections() {
        logisticsSection.classList.add('hidden');
        guestSection.classList.add('hidden');
        achievementSection.classList.add('hidden');
        visitSection.classList.add('hidden');
    }

    reportTypeSelect.addEventListener('change', function () {
        hideAllSections();
        const selectedValue = this.value;

        if (selectedValue === 'academic') {
            logisticsSection.classList.remove('hidden');
        }
        else if (selectedValue === 'guest') {
            logisticsSection.classList.remove('hidden');
            guestSection.classList.remove('hidden');
        }
        else if (selectedValue === 'achievement') {
            achievementSection.classList.remove('hidden');
        }
        else if (selectedValue === 'visit') {
            visitSection.classList.remove('hidden');
        }
    });

    // ── Submit Handling ──
    const reportForm = document.getElementById('reportForm');
    const submitBtn = document.getElementById('submitBtn');

    reportForm.addEventListener('submit', function (e) {
        // Perform standard HTML5 validity validation
        if (!reportForm.checkValidity()) {
            e.preventDefault();
            reportForm.reportValidity();
            return;
        }

        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Submitting...';
    });
});
