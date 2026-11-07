/**
 * CTL Activity Dashboard Controller
 * Department of CE & IT — Smart Management Dashboard
 *
 * Capabilities:
 * - Master Excel Sheet Dropzone & SheetJS (.xlsx, .xls) Parsing
 * - Dynamic Column & Header Auto-Detection with Clean Date Formatter
 * - KPI Metric Cards Counter Synchronization
 * - Multi-Criteria Real-Time Filtering (Approval Status, Submission Condition, Search)
 * - Faculty Coordinator Search Dropdown & Auto-fill
 * - CC Email Tag Management & Defaults
 * - HTML Email Report Generator & Dispatch to send-email.php
 * - Direct ERP Quick Access Modal with Live Search & Semester Filtering
 * - Universal Toast Notifications & High-Resolution UI Feedback
 */

(function () {
  'use strict';

  // --- STATE VARIABLES ---
  let allData = [];
  let activeData = [];
  let selectedFaculty = null;
  let activeFile = null;

  const defaultCCEmails = [
    'drchandarana@gmiu.edu.in', // HOD (Prof. Dhaval Chandarana)
    'sbchauhan@gmiu.edu.in',    // Incharge HOD IT (Prof. Shwetaba Chauhan)
    'ehunagar@gmiu.edu.in',     // Incharge HOD CE (Prof. Ekta Unagar)
    'tmvyas@gmiu.edu.in'        // Sub Incharge HOD IT (Prof. Tarjanee Vyas)
  ];
  let selectedCCEmails = [...defaultCCEmails];

  // --- 1. TOAST NOTIFICATION HELPER ---
  function showToast(message, isSuccess = true) {
    const banner = document.getElementById('toastBanner');
    const msgEl = document.getElementById('toastMessage');
    const dot = document.getElementById('toastDot');

    if (!banner || !msgEl) {
      if (typeof window.showPortalToast === 'function') {
        window.showPortalToast(message);
      }
      return;
    }

    msgEl.textContent = message;
    if (dot) {
      dot.className = isSuccess
        ? 'w-2 h-2 rounded-full bg-[var(--color-primary)]'
        : 'w-2 h-2 rounded-full bg-[var(--color-alert)]';
    }

    banner.classList.remove('hidden');
    banner.classList.add('flex', 'opacity-100', 'translate-y-0');

    clearTimeout(window.__ctlToastTimeout);
    window.__ctlToastTimeout = setTimeout(() => {
      banner.classList.remove('opacity-100', 'translate-y-0');
      banner.classList.add('hidden');
    }, 3200);
  }
  window.showCtlToast = showToast;

  // --- 2. AVATAR STYLING HELPER ---
  const getAvatarClass = (member) => {
    const legacyClasses = ['av-dc', 'av-sw', 'av-eu', 'av-tv'];
    if (member.avatarClass && legacyClasses.includes(member.avatarClass)) {
      return member.avatarClass;
    }
    const colors = [
      'av-theme-red', 'av-theme-blue', 'av-theme-purple',
      'av-theme-teal', 'av-theme-green', 'av-theme-orange',
      'av-theme-indigo', 'av-theme-cyan', 'av-theme-pink'
    ];
    let hash = 0;
    const name = member.name || '';
    for (let i = 0; i < name.length; i++) {
      hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % colors.length;
    return colors[index];
  };

  function getCompactDesignation(desg) {
    if (!desg) return '';
    const lower = desg.toLowerCase();
    if (lower === 'hod' || lower.includes('both')) return 'HOD';
    if (lower.includes('incharge hod it')) return 'HOD IT';
    if (lower.includes('incharge hod ce')) return 'HOD CE';
    if (lower.includes('sub incharge hod it')) return 'Sub HOD IT';
    if (lower.includes('sub incharge hod ce')) return 'Sub HOD CE';
    if (lower.includes('assistant professor')) return 'Asst. Prof';
    if (lower.includes('associate professor')) return 'Assoc. Prof';
    if (lower.includes('teaching assistant')) return 'TA';
    if (lower.includes('lecturer')) return 'Lecturer';
    return desg;
  }

  // --- 3. EXCEL DATA HELPERS & TOKEN EXTRACTORS ---
  function cleanText(str) {
    if (str === undefined || str === null) return '';
    return String(str)
      .replace(/[\r\n\t]+/g, ' ')
      .replace(/\s{2,}/g, ' ')
      .trim();
  }

  function formatExcelDate(cell) {
    if (cell === undefined || cell === null || cell === '') return '';

    if (cell instanceof Date && !isNaN(cell.getTime())) {
      const day = String(cell.getDate()).padStart(2, '0');
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      const month = months[cell.getMonth()];
      const year = cell.getFullYear();
      return `${day} ${month} ${year}`;
    }

    if (typeof cell === 'number' && cell > 20000 && cell < 80000) {
      const utcDays = Math.floor(cell - 25569);
      const utcValue = utcDays * 86400;
      const dateInfo = new Date(utcValue * 1000);
      const day = String(dateInfo.getUTCDate()).padStart(2, '0');
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      const month = months[dateInfo.getUTCMonth()];
      const year = dateInfo.getUTCFullYear();
      return `${day} ${month} ${year}`;
    }

    let str = cleanText(cell);
    const isoMatch = str.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (isoMatch) {
      const year = isoMatch[1];
      const monthIdx = parseInt(isoMatch[2], 10) - 1;
      const day = isoMatch[3];
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      return `${day} ${months[monthIdx] || isoMatch[2]} ${year}`;
    }

    return str;
  }

  function extractSubmissionTokens(text) {
    if (!text) return [];
    const str = String(text).trim();
    if (!str) return [];

    const parts = str
      .split(/[•,\n\r|;]+/)
      .map(p => p.replace(/^[•\s]+/, '').trim())
      .filter(p => p.length > 0);

    const result = [];
    parts.forEach(part => {
      const lower = part.toLowerCase();
      if (
        (lower.includes('submitted') || lower.includes('submited') || lower.includes('grace period') || lower.includes('past planning')) &&
        !part.includes('•') && !part.includes(',') && !part.includes(';')
      ) {
        const tokenRegex = /(not submited|not submitted|submitted|submited|on time|over grace period|past planning date \d+ days?|past planning date|\d+ days?|delayed)/gi;
        const matches = part.match(tokenRegex);
        if (matches && matches.length > 0) {
          const seen = new Set();
          matches.forEach(m => {
            let cleanM = m.trim();
            const key = cleanM.toLowerCase().replace('submited', 'submitted');
            if (!seen.has(key)) {
              seen.add(key);
              if (cleanM.toLowerCase() === 'not submited') cleanM = 'Not Submitted';
              if (cleanM.toLowerCase() === 'submited') cleanM = 'Submitted';
              result.push(cleanM);
            }
          });
          return;
        }
      }
      result.push(part);
    });

    return result;
  }

  // Helper pills for table rows
  function getSubmissionPills(text) {
    const parts = extractSubmissionTokens(text);

    if (!parts || parts.length === 0) {
      return `<span class="ctl-pill ctl-pill-muted">-</span>`;
    }

    return `<div class="flex items-center justify-center flex-wrap gap-1">` + parts.map(part => {
      const lower = part.toLowerCase();
      let cls = 'ctl-pill-muted';

      if (lower.includes('not submitted') || lower.includes('not submited')) {
        cls = 'ctl-pill-danger';
      } else if (lower.includes('over grace period')) {
        cls = 'ctl-pill-alert';
      } else if (lower.includes('past planning date') || lower.includes('delayed')) {
        cls = 'ctl-pill-warning';
      } else if (lower.includes('on time') || lower.includes('submitted') || lower.includes('submited')) {
        cls = 'ctl-pill-info';
      }

      return `<span class="ctl-pill ${cls}">${part}</span>`;
    }).join('') + `</div>`;
  }

  function getApprovalPill(text) {
    const cleanVal = (text || '').replace(/^[•\s]+/, '').trim();
    const lower = cleanVal.toLowerCase();

    if (lower.includes('approved')) return `<span class="ctl-pill ctl-pill-success">Approved</span>`;
    if (lower.includes('rejected') || lower.includes('reject')) return `<span class="ctl-pill ctl-pill-danger">Reject</span>`;

    return `<span class="ctl-pill ctl-pill-warning">Pending</span>`;
  }

  // --- 4. EXCEL UPLOAD & SHEETJS PARSER ---
  function initExcelUploader() {
    const uploadArea = document.getElementById('uploadArea');
    const excelFile = document.getElementById('excelFile');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const fileLoadedBadge = document.getElementById('fileLoadedBadge');
    const loadedFileName = document.getElementById('loadedFileName');
    const loadedFileSize = document.getElementById('loadedFileSize');
    const replaceFileBtn = document.getElementById('replaceFileBtn');

    if (!uploadArea || !excelFile) return;

    uploadArea.addEventListener('click', (e) => {
      if (e.target === replaceFileBtn || e.target.closest('#replaceFileBtn')) {
        excelFile.click();
        return;
      }
      excelFile.click();
    });

    uploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      e.stopPropagation();
      uploadArea.classList.add('ctl-dropzone-dragover');
    });

    uploadArea.addEventListener('dragleave', (e) => {
      e.preventDefault();
      e.stopPropagation();
      uploadArea.classList.remove('ctl-dropzone-dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      e.stopPropagation();
      uploadArea.classList.remove('ctl-dropzone-dragover');

      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        excelFile.files = e.dataTransfer.files;
        handleFileLoad(excelFile.files[0]);
      }
    });

    excelFile.addEventListener('change', (e) => {
      if (e.target.files && e.target.files.length > 0) {
        handleFileLoad(e.target.files[0]);
      }
    });

    if (replaceFileBtn) {
      replaceFileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        excelFile.click();
      });
    }
  }

  function handleFileLoad(file) {
    if (!file) return;

    // Check extension
    const ext = file.name.split('.').pop().toLowerCase();
    if (ext !== 'xlsx' && ext !== 'xls') {
      alert('Please select a valid Excel file (.xlsx or .xls)');
      return;
    }

    if (typeof XLSX === 'undefined') {
      alert('Excel parsing engine (SheetJS) is still loading. Please try again in a moment.');
      return;
    }

    // Check server error trigger config if set
    if (typeof ctlExcelServerError !== 'undefined' && ctlExcelServerError === true) {
      const content = document.getElementById('mainDashboardContent');
      const errContainer = document.getElementById('fullServerErrorContainer');
      if (content) content.style.display = 'none';
      if (errContainer) errContainer.style.display = 'flex';
      return;
    }

    activeFile = file;
    const uploadPrompt = document.getElementById('uploadPrompt');
    const fileLoadedBadge = document.getElementById('fileLoadedBadge');
    const loadedFileName = document.getElementById('loadedFileName');
    const loadedFileSize = document.getElementById('loadedFileSize');

    if (loadedFileName) loadedFileName.textContent = file.name;
    if (loadedFileSize) {
      const kb = (file.size / 1024).toFixed(1);
      loadedFileSize.textContent = `${kb} KB`;
    }

    if (uploadPrompt) uploadPrompt.classList.add('hidden');
    if (fileLoadedBadge) fileLoadedBadge.classList.remove('hidden');

    const reader = new FileReader();
    reader.onload = function (evt) {
      try {
        const data = new Uint8Array(evt.target.result);
        const workbook = XLSX.read(data, { type: 'array', cellDates: true });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];

        const rows = XLSX.utils.sheet_to_json(sheet, {
          header: 1,
          defval: ''
        });

        allData = [];

        let headerRowIndex = 1;
        let colIndices = {
          srNo: 0,
          name: 1,
          planDate: 2,
          actualDate: 3,
          modifiedDate: 4,
          marks: 5,
          flags: 6,
          submissionFlags: 7,
          approvalStatus: 8
        };

        // Scan rows to find the exact header row
        for (let i = 0; i < Math.min(10, rows.length); i++) {
          const row = rows[i];
          if (!row || row.length === 0) continue;

          let hasName = false;
          let hasSub = false;
          row.forEach(cell => {
            const str = String(cell).trim().toLowerCase();
            if (str === 'name') hasName = true;
            if (str.includes('submission flag') || str.includes('submission flags') || str.includes('submission')) hasSub = true;
          });

          if (hasName && hasSub) {
            headerRowIndex = i;
            colIndices = {
              srNo: -1,
              name: -1,
              planDate: -1,
              actualDate: -1,
              modifiedDate: -1,
              marks: -1,
              flags: -1,
              submissionFlags: -1,
              approvalStatus: -1
            };

            row.forEach((cell, idx) => {
              const str = String(cell).trim().toLowerCase();
              if (str === '#') colIndices.srNo = idx;
              else if (str === 'name') colIndices.name = idx;
              else if (str === 'plan date') colIndices.planDate = idx;
              else if (str === 'actual date') colIndices.actualDate = idx;
              else if (str === 'modified date') colIndices.modifiedDate = idx;
              else if (str === 'marks') colIndices.marks = idx;
              else if (str === 'flags') colIndices.flags = idx;
              else if (str.includes('submission flag') || str.includes('submission flags') || str.includes('submission')) colIndices.submissionFlags = idx;
              else if (str.includes('approval status') || str === 'approval' || str.includes('approval')) colIndices.approvalStatus = idx;
            });
            break;
          }
        }

        // Parse data rows
        let autoSrNo = 1;
        for (let i = headerRowIndex + 1; i < rows.length; i++) {
          const row = rows[i];
          if (!row || colIndices.name === -1 || !row[colIndices.name]) continue;

          const rawSr = colIndices.srNo !== -1 && row[colIndices.srNo] !== undefined ? cleanText(row[colIndices.srNo]) : '';
          const finalSr = rawSr ? rawSr : autoSrNo;
          autoSrNo++;

          allData.push({
            srNo: finalSr,
            name: cleanText(row[colIndices.name]),
            planDate: colIndices.planDate !== -1 && row[colIndices.planDate] !== undefined ? formatExcelDate(row[colIndices.planDate]) : '',
            actualDate: colIndices.actualDate !== -1 && row[colIndices.actualDate] !== undefined ? formatExcelDate(row[colIndices.actualDate]) : '',
            modifiedDate: colIndices.modifiedDate !== -1 && row[colIndices.modifiedDate] !== undefined ? formatExcelDate(row[colIndices.modifiedDate]) : '',
            marks: colIndices.marks !== -1 && row[colIndices.marks] !== undefined ? cleanText(row[colIndices.marks]) : '',
            flags: colIndices.flags !== -1 && row[colIndices.flags] !== undefined ? cleanText(row[colIndices.flags]) : '',
            submissionFlags: colIndices.submissionFlags !== -1 && row[colIndices.submissionFlags] !== undefined ? cleanText(row[colIndices.submissionFlags]) : '',
            approvalStatus: colIndices.approvalStatus !== -1 && row[colIndices.approvalStatus] !== undefined ? cleanText(row[colIndices.approvalStatus]) : ''
          });
        }

        // Populate submissionFilter select dropdown dynamically
        const subFilterSelect = document.getElementById('submissionFilter');
        if (subFilterSelect) {
          const defaultOptions = ['Submitted', 'Not Submitted', 'On Time', 'Delayed', 'Over Grace Period'];
          const foundTags = new Set();
          allData.forEach(item => {
            if (item.submissionFlags) {
              const parts = item.submissionFlags.split(/[•,\n\r|;]+/).map(p => p.replace(/^[•\s]+/, '').trim()).filter(p => p.length > 0);
              parts.forEach(p => foundTags.add(p));
            }
          });

          let optionsHtml = `
            <option value="">All Conditions</option>
            <option value="Submitted">Submitted (Any)</option>
            <option value="Not Submitted">Not Submitted</option>
            <option value="On Time">On Time</option>
            <option value="Delayed">Delayed</option>
            <option value="Over Grace Period">Over Grace Period</option>
          `;

          foundTags.forEach(tag => {
            const exists = defaultOptions.some(opt => opt.toLowerCase() === tag.toLowerCase());
            if (!exists) {
              optionsHtml += `<option value="${tag}">${tag}</option>`;
            }
          });
          subFilterSelect.innerHTML = optionsHtml;
        }

        updateCards(allData);
        renderTable(allData);
        activeData = [...allData];

        const totalBadge = document.getElementById('totalActivitiesBadge');
        if (totalBadge) {
          totalBadge.textContent = `${allData.length} Activities Loaded`;
        }

        showToast(`Master Sheet uploaded: ${allData.length} activities parsed!`);
      } catch (err) {
        console.error('Error parsing Excel:', err);
        alert('Could not parse Excel file. Error: ' + err.message);
      }
    };

    reader.readAsArrayBuffer(file);
  }

  // --- 5. KPI METRICS CARDS & TABLE RENDERING ---
  function updateCards(data) {
    const totalEl = document.getElementById('total');
    if (totalEl) totalEl.innerText = data.length;

    const submittedCount = data.filter(x => {
      const sub = (x.submissionFlags || '').toLowerCase();
      return (sub.includes('submitted') || sub.includes('submited') || sub.includes('on time') || sub.includes('delayed')) && !sub.includes('not submitted') && !sub.includes('not submited');
    }).length;

    const notSubmittedCount = data.filter(x => {
      const sub = (x.submissionFlags || '').toLowerCase();
      return sub.includes('not submitted') || sub.includes('not submited');
    }).length;

    const approvedCount = data.filter(x => (x.approvalStatus || '').toLowerCase().includes('approved')).length;
    const pendingCount = data.filter(x => {
      const app = (x.approvalStatus || '').toLowerCase();
      return app.includes('pending') || (!app && !app.includes('approved') && !app.includes('reject') && !app.includes('rejected'));
    }).length;
    const rejectedCount = data.filter(x => (x.approvalStatus || '').toLowerCase().includes('rejected') || (x.approvalStatus || '').toLowerCase().includes('reject')).length;

    if (document.getElementById('submittedCount')) document.getElementById('submittedCount').innerText = submittedCount;
    if (document.getElementById('notSubmittedCount')) document.getElementById('notSubmittedCount').innerText = notSubmittedCount;
    if (document.getElementById('approved')) document.getElementById('approved').innerText = approvedCount;
    if (document.getElementById('pending')) document.getElementById('pending').innerText = pendingCount;
    if (document.getElementById('rejected')) document.getElementById('rejected').innerText = rejectedCount;
  }

  function renderTable(data) {
    const tbody = document.getElementById('tbody');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
      if (allData.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="9" class="p-8 text-center">
              <div class="flex flex-col items-center justify-center gap-2 text-[var(--text-muted)]">
                <svg class="w-10 h-10 opacity-40 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
                <p class="font-bold text-sm text-[var(--text-primary)]">Awaiting Master Sheet Upload</p>
                <p class="text-xs">Upload your Excel master sheet (.xlsx) above to preview and track activities.</p>
              </div>
            </td>
          </tr>
        `;
      } else {
        tbody.innerHTML = `
          <tr>
            <td colspan="9" class="p-8 text-center text-xs text-[var(--text-muted)]">
              No activities found matching your current filter criteria.
            </td>
          </tr>
        `;
      }
      return;
    }

    data.forEach(item => {
      const tr = document.createElement('tr');
      tr.className = 'ctl-table-row border-b border-black/5 dark:border-white/5 hover:bg-black/[0.02] dark:hover:bg-white/[0.03] transition-colors';

      tr.innerHTML = `
        <td class="px-3 py-2.5 text-center font-mono text-xs text-[var(--text-muted)] font-bold">${item.srNo}</td>
        <td class="px-3 py-2.5 text-xs font-semibold text-[var(--text-primary)] min-w-[200px]">${item.name}</td>
        <td class="px-3 py-2.5 text-center text-xs text-[var(--text-secondary)] font-mono whitespace-nowrap">${item.planDate || '-'}</td>
        <td class="px-3 py-2.5 text-center text-xs text-[var(--text-secondary)] font-mono whitespace-nowrap">${item.actualDate || '-'}</td>
        <td class="px-3 py-2.5 text-center text-xs text-[var(--text-muted)] font-mono whitespace-nowrap">${item.modifiedDate || '-'}</td>
        <td class="px-3 py-2.5 text-center text-xs font-bold text-[var(--text-primary)]">${item.marks || '-'}</td>
        <td class="px-3 py-2.5 text-center text-xs text-[var(--text-muted)]">${item.flags || '-'}</td>
        <td class="px-3 py-2.5 text-center">${getSubmissionPills(item.submissionFlags)}</td>
        <td class="px-3 py-2.5 text-center whitespace-nowrap">${getApprovalPill(item.approvalStatus)}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  function filterData() {
    const statusSelect = document.getElementById('statusFilter');
    const submissionSelect = document.getElementById('submissionFilter');
    const searchInput = document.getElementById('search');

    const status = statusSelect ? statusSelect.value.toLowerCase().trim() : '';
    const submission = submissionSelect ? submissionSelect.value.toLowerCase().trim() : '';
    const search = searchInput ? searchInput.value.toLowerCase().trim() : '';

    const filtered = allData.filter(item => {
      const itemApproval = (item.approvalStatus || '').toLowerCase();
      const itemSubRaw = (item.submissionFlags || '').toLowerCase();
      const itemAct = (item.name || '').toLowerCase();
      const tokens = extractSubmissionTokens(item.submissionFlags).map(t => t.toLowerCase());

      const isNotSubmitted = itemSubRaw.includes('not submitted') || itemSubRaw.includes('not submited') || tokens.some(t => t.includes('not submitted') || t.includes('not submited'));

      // Status Match Logic
      let statusMatch = true;
      if (status) {
        if (status.includes('reject')) {
          statusMatch = itemApproval.includes('reject') || itemApproval.includes('rejected');
        } else if (status.includes('approved')) {
          statusMatch = itemApproval.includes('approved');
        } else if (status.includes('pending')) {
          statusMatch = itemApproval.includes('pending') || (!itemApproval.includes('approved') && !(itemApproval.includes('reject') || itemApproval.includes('rejected')));
        } else {
          statusMatch = itemApproval.includes(status);
        }
      }

      // Submission Match Logic
      let submissionMatch = true;
      if (submission) {
        if (submission === 'not submitted' || submission === 'not submited') {
          submissionMatch = isNotSubmitted;
        } else if (submission === 'submitted' || submission === 'submited') {
          submissionMatch = !isNotSubmitted && (itemSubRaw.includes('submitted') || itemSubRaw.includes('submited') || itemSubRaw.includes('on time') || itemSubRaw.includes('delayed') || tokens.some(t => t.includes('submitted') || t.includes('on time') || t.includes('delayed')));
        } else if (submission === 'on time') {
          submissionMatch = itemSubRaw.includes('on time') || itemSubRaw.includes('ontime') || itemSubRaw.includes('on-time') || tokens.some(t => t.includes('on time') || t.includes('ontime'));
        } else {
          submissionMatch = itemSubRaw.includes(submission) || tokens.some(t => t.includes(submission) || submission.includes(t));
        }
      }

      const searchMatch = !search || itemAct.includes(search);

      return statusMatch && submissionMatch && searchMatch;
    });

    renderTable(filtered);
    activeData = filtered;
  }

  // --- 6. FACULTY SEARCH & AUTOFILL ---
  function initFacultySearch() {
    const facultySearch = document.getElementById('facultySearch');
    const preparedBy = document.getElementById('preparedBy');
    const facultyDropdownList = document.getElementById('facultyDropdownList');
    const facultyError = document.getElementById('facultyError');
    const facultyEmail = document.getElementById('facultyEmail');
    const facultyPhone = document.getElementById('facultyPhone');

    if (!facultySearch || typeof facultyData === 'undefined') return;

    renderDropdown(facultyData);

    facultySearch.addEventListener('focus', function () {
      facultyDropdownList.classList.add('show');
      filterFaculty();
    });

    facultySearch.addEventListener('input', function () {
      facultyDropdownList.classList.add('show');
      filterFaculty();
      if (this.value.trim() === '') {
        clearSelection();
      }
    });

    document.addEventListener('click', function (e) {
      if (!e.target.closest('.search-select-wrap')) {
        facultyDropdownList.classList.remove('show');
        validateSearchInput();
      }
    });

    function filterFaculty() {
      const query = facultySearch.value.toLowerCase().replace('prof.', '').replace('mr.', '').replace('dr.', '').trim();
      const filtered = facultyData.filter(member =>
        member.name.toLowerCase().includes(query) ||
        member.initials.toLowerCase().includes(query) ||
        member.email.toLowerCase().includes(query) ||
        member.designation.toLowerCase().includes(query) ||
        member.empId.toLowerCase().includes(query)
      );
      renderDropdown(filtered);
    }

    function renderDropdown(list) {
      facultyDropdownList.innerHTML = '';
      if (list.length === 0) {
        facultyDropdownList.innerHTML = `<div class="p-3 text-xs text-[var(--text-muted)] text-center">No faculty members found</div>`;
        return;
      }

      list.forEach(member => {
        const item = document.createElement('div');
        item.className = 'ctl-dropdown-item flex items-center gap-2.5 px-3 py-2 cursor-pointer hover:bg-black/5 dark:hover:bg-white/10 transition';
        item.innerHTML = `
          <div class="ctl-avatar-circle ${getAvatarClass(member)} w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0">${member.initials}</div>
          <div class="min-w-0 flex-1">
            <div class="text-xs font-bold text-[var(--text-primary)] leading-tight truncate">${member.name} <span class="font-normal opacity-70">(${member.initials})</span></div>
            <div class="text-[10.5px] text-[var(--text-muted)] truncate">${member.email} • ${member.designation} • ${member.department || 'IT'}</div>
          </div>
        `;
        item.addEventListener('click', function () {
          selectFaculty(member);
        });
        facultyDropdownList.appendChild(item);
      });
    }

    function selectFaculty(member) {
      facultySearch.value = member.name;
      preparedBy.value = member.name;
      selectedFaculty = member;

      if (facultyEmail) facultyEmail.value = member.email;
      if (facultyPhone) facultyPhone.value = '+91 ' + member.phone;

      facultySearch.classList.remove('ctl-input-error');
      if (facultyError) facultyError.classList.add('hidden');
      facultyDropdownList.classList.remove('show');

      // Update document title dynamically
      const isCe = (member.initials === 'DRC' || member.name.includes('Dhaval Chandarana'))
        ? (localStorage.getItem('portal_dept') === 'CE')
        : (member.department === 'Computer Engineering');
      document.title = isCe ? 'CTL Activity — CE Department' : 'CTL Activity — IT Department';
    }

    function clearSelection() {
      preparedBy.value = '';
      selectedFaculty = null;
      if (facultyEmail) facultyEmail.value = '';
      if (facultyPhone) facultyPhone.value = '';
    }

    function validateSearchInput() {
      const val = facultySearch.value.trim();
      if (val === '') {
        clearSelection();
        return;
      }

      const valLower = val.toLowerCase();
      const match = facultyData.find(m =>
        m.name.toLowerCase() === valLower ||
        m.initials.toLowerCase() === valLower ||
        m.email.toLowerCase() === valLower
      );

      if (match) {
        selectFaculty(match);
      } else {
        const partialMatches = facultyData.filter(m =>
          m.name.toLowerCase().includes(valLower) ||
          m.initials.toLowerCase().includes(valLower) ||
          m.email.toLowerCase().includes(valLower)
        );
        if (partialMatches.length === 1) {
          selectFaculty(partialMatches[0]);
        } else {
          clearSelection();
          facultySearch.value = '';
        }
      }
    }
  }

  // --- 7. CC EMAILS MULTI-SELECT ---
  function updateCCTagsUI() {
    const ccTagsContainer = document.getElementById('ccTagsContainer');
    if (!ccTagsContainer) return;

    ccTagsContainer.innerHTML = '';
    selectedCCEmails.forEach(email => {
      const member = (typeof facultyData !== 'undefined')
        ? facultyData.find(m => m.email.toLowerCase() === email.toLowerCase())
        : null;

      let displayName = email;
      let tooltipText = email;
      if (member) {
        const shortDesg = getCompactDesignation(member.designation);
        displayName = shortDesg ? `${member.name} (${shortDesg})` : member.name;
        tooltipText = `${member.name} — ${member.designation || ''} (${email})`;
      }

      const tag = document.createElement('div');
      tag.className = 'ctl-cc-tag inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full glass-pill text-[11px] font-semibold text-[var(--text-primary)] border border-white/60 dark:border-white/10 shrink-0';
      tag.title = tooltipText;
      tag.innerHTML = `
        <span class="truncate max-w-[150px]">${displayName}</span>
        <button type="button" class="ctl-tag-remove text-[var(--text-muted)] hover:text-red-500 font-bold ml-0.5" title="Remove">&times;</button>
      `;
      tag.querySelector('.ctl-tag-remove').addEventListener('click', function (e) {
        e.stopPropagation();
        removeCCTag(email);
      });
      ccTagsContainer.appendChild(tag);
    });
  }

  function removeCCTag(email) {
    selectedCCEmails = selectedCCEmails.filter(e => e !== email);
    updateCCTagsUI();
  }

  function resetCCEmailsToDefault() {
    selectedCCEmails = [...defaultCCEmails];
    updateCCTagsUI();
    showToast('CC recipients reset to defaults');
  }

  function initCCEmailsSearch() {
    const ccSearch = document.getElementById('ccSearch');
    const ccDropdownList = document.getElementById('ccDropdownList');
    const ccSelectWrap = document.querySelector('.cc-select-wrap');
    const resetCCBtn = document.getElementById('resetCCBtn');

    if (!ccSearch || !ccDropdownList) return;

    updateCCTagsUI();
    renderCCDropdown(typeof facultyData !== 'undefined' ? facultyData : []);

    if (resetCCBtn) {
      resetCCBtn.addEventListener('click', resetCCEmailsToDefault);
    }

    if (ccSelectWrap) {
      ccSelectWrap.addEventListener('click', function (e) {
        if (e.target === ccSelectWrap) ccSearch.focus();
      });
    }

    ccSearch.addEventListener('focus', function () {
      ccDropdownList.classList.add('show');
      filterCC();
    });

    ccSearch.addEventListener('input', function () {
      ccDropdownList.classList.add('show');
      filterCC();
    });

    ccSearch.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const val = this.value.replace(',', '').trim();
        if (val && val.includes('@')) {
          addCCTag(val);
          this.value = '';
          ccDropdownList.classList.remove('show');
        }
      }
    });

    document.addEventListener('click', function (e) {
      if (!e.target.closest('.cc-select-wrap')) {
        ccDropdownList.classList.remove('show');
      }
    });

    function filterCC() {
      const q = ccSearch.value.toLowerCase().trim();
      const list = (typeof facultyData !== 'undefined' ? facultyData : []).filter(m =>
        m.name.toLowerCase().includes(q) ||
        m.email.toLowerCase().includes(q) ||
        m.initials.toLowerCase().includes(q)
      );
      renderCCDropdown(list);
    }

    function renderCCDropdown(list) {
      ccDropdownList.innerHTML = '';
      if (list.length === 0) {
        ccDropdownList.innerHTML = `<div class="p-3 text-xs text-[var(--text-muted)] text-center">No matches found</div>`;
        return;
      }

      list.forEach(member => {
        const isAdded = selectedCCEmails.includes(member.email);
        const item = document.createElement('div');
        item.className = `ctl-dropdown-item flex items-center justify-between gap-2 px-3 py-2 cursor-pointer hover:bg-black/5 dark:hover:bg-white/10 transition ${isAdded ? 'opacity-50' : ''}`;
        item.innerHTML = `
          <div class="min-w-0">
            <div class="text-xs font-bold text-[var(--text-primary)] truncate">${member.name} (${member.initials})</div>
            <div class="text-[10px] text-[var(--text-muted)] truncate">${member.email}</div>
          </div>
          <span class="text-[11px] font-bold ${isAdded ? 'text-green-500' : 'text-[var(--color-primary)]'}">${isAdded ? 'Added' : '+ Add'}</span>
        `;
        item.addEventListener('click', function (e) {
          e.stopPropagation();
          if (!isAdded) {
            addCCTag(member.email);
            ccSearch.value = '';
            ccDropdownList.classList.remove('show');
          }
        });
        ccDropdownList.appendChild(item);
      });
    }

    function addCCTag(email) {
      if (!selectedCCEmails.includes(email)) {
        selectedCCEmails.push(email);
        updateCCTagsUI();
      }
    }
  }

  // --- 8. EMAIL DISPATCH ACTION ---
  function initEmailDispatch() {
    const sendBtn = document.getElementById('sendBtn');
    if (!sendBtn) return;

    sendBtn.addEventListener('click', function () {
      if (allData.length === 0) {
        alert('Please upload an Excel master sheet first before sending the activity report.');
        return;
      }

      const preparedBy = document.getElementById('preparedBy')?.value;
      if (!preparedBy) {
        const facultySearch = document.getElementById('facultySearch');
        const facultyError = document.getElementById('facultyError');
        if (facultySearch) {
          facultySearch.classList.add('ctl-input-error');
          facultySearch.focus();
        }
        if (facultyError) facultyError.classList.remove('hidden');
        alert('Please select a faculty coordinator from the dropdown before sending.');
        return;
      }

      sendBtn.disabled = true;
      sendBtn.innerHTML = `
        <svg class="w-4 h-4 animate-spin shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span>Sending...</span>
      `;

      const isCe = (selectedFaculty && (selectedFaculty.initials === 'DRC' || selectedFaculty.name.includes('Dhaval Chandarana'))
        ? (localStorage.getItem('portal_dept') === 'CE')
        : (selectedFaculty && selectedFaculty.department === 'Computer Engineering'));

      // Helper pills for HTML email
      function getApprovalPillHtmlForEmail(text) {
        let cleanVal = (text || '').replace(/^[•\s]+/, '').trim();
        const lower = cleanVal.toLowerCase();
        let bg = '#fef3c7';
        let fg = '#92400e';
        let label = 'Pending';

        if (lower.includes('approved')) {
          bg = '#d1fae5'; fg = '#065f46'; label = 'Approved';
        } else if (lower.includes('rejected') || lower.includes('reject')) {
          bg = '#fee2e2'; fg = '#991b1b'; label = 'Reject';
        }

        return `<span style="display: inline-block; padding: 4px 10px; font-size: 10px; font-weight: 700; border-radius: 9999px; background-color: ${bg}; color: ${fg}; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">${label}</span>`;
      }

      function getSubmissionPillsHtmlForEmail(text) {
        const parts = extractSubmissionTokens(text);
        if (!parts || parts.length === 0) {
          return `<span style="display: inline-block; padding: 4px 10px; font-size: 10px; font-weight: 700; border-radius: 9999px; background-color: #f1f5f9; color: #475569; font-family: 'Playfair Display', Georgia, serif;">-</span>`;
        }

        return `<div style="text-align: center; font-family: 'Playfair Display', Georgia, serif;">` + parts.map(part => {
          const lower = part.toLowerCase();
          let bg = '#f1f5f9';
          let fg = '#475569';

          if (lower.includes('not submitted') || lower.includes('not submited')) {
            bg = '#fee2e2'; fg = '#991b1b';
          } else if (lower.includes('over grace period')) {
            bg = '#fca5a5'; fg = '#7f1d1d';
          } else if (lower.includes('past planning date') || lower.includes('delayed')) {
            bg = '#ffedd5'; fg = '#9a3412';
          } else if (lower.includes('on time') || lower.includes('submitted') || lower.includes('submited')) {
            bg = '#dbeafe'; fg = '#1e40af';
          }

          return `<div style="margin: 4px 0;"><span style="display: inline-block; padding: 4px 8px; font-size: 9px; font-weight: 700; border-radius: 9999px; background-color: ${bg}; color: ${fg}; text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap; font-family: 'Playfair Display', Georgia, serif;">${part}</span></div>`;
        }).join('') + `</div>`;
      }

      const activeTotal = allData.length;
      const activeSubmitted = allData.filter(x => {
        const sub = (x.submissionFlags || '').toLowerCase();
        return (sub.includes('submitted') || sub.includes('submited') || sub.includes('on time') || sub.includes('delayed')) && !sub.includes('not submitted') && !sub.includes('not submited');
      }).length;
      const activeNotSubmitted = allData.filter(x => {
        const sub = (x.submissionFlags || '').toLowerCase();
        return sub.includes('not submitted') || sub.includes('not submited');
      }).length;
      const activeApproved = allData.filter(x => (x.approvalStatus || '').toLowerCase().includes('approved')).length;
      const activePending = allData.filter(x => {
        const app = (x.approvalStatus || '').toLowerCase();
        return app.includes('pending') || (!app && !app.includes('approved') && !app.includes('reject') && !app.includes('rejected'));
      }).length;
      const activeRejected = allData.filter(x => (x.approvalStatus || '').toLowerCase().includes('rejected') || (x.approvalStatus || '').toLowerCase().includes('reject')).length;

      let tableRowsHtml = '';
      const dataToExport = activeData.length > 0 ? activeData : allData;
      dataToExport.forEach((item, index) => {
        const bg = index % 2 === 0 ? '#ffffff' : '#f8fafc';
        tableRowsHtml += `
          <tr style="background-color: ${bg};">
            <td style="padding: 12px 14px; text-align: center; font-weight: 500; color: #64748b; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${index + 1}</td>
            <td style="padding: 12px 14px; font-weight: 600; color: #0f172a; word-break: break-word; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${item.name}</td>
            <td style="padding: 12px 14px; text-align: center; color: #334155; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${item.planDate || '-'}</td>
            <td style="padding: 12px 14px; text-align: center; color: #334155; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${item.actualDate || '-'}</td>
            <td style="padding: 12px 14px; text-align: center; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${getSubmissionPillsHtmlForEmail(item.submissionFlags)}</td>
            <td style="padding: 12px 14px; text-align: center; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${getApprovalPillHtmlForEmail(item.approvalStatus)}</td>
          </tr>
        `;
      });

      const emailHtml = `
        <!DOCTYPE html>
        <html>
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <style>
            body, table, td, th, div, span, p, a, h1, h2 { font-family: 'Playfair Display', Georgia, serif !important; }
            @media only screen and (max-width: 600px) {
              .email-container { padding: 12px 6px !important; }
              .card-wrapper { border-radius: 12px !important; }
              .header-banner { padding: 24px 16px !important; }
              .header-banner h1 { font-size: 20px !important; }
              .faculty-card { margin: 16px 12px 0 12px !important; padding: 12px !important; }
              .section-container { margin: 20px 12px 0 12px !important; }
              .metrics-table tr, .metrics-table td { display: block !important; width: 100% !important; }
              .metrics-card { margin-bottom: 8px !important; }
              .table-responsive { display: block !important; width: 100% !important; overflow-x: auto !important; }
            }
          </style>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Playfair Display', Georgia, serif; color: #334155;">
          <div class="email-container" style="background-color: #f8fafc; padding: 36px 16px;">
            <div class="card-wrapper" style="max-width: 1050px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);">
              <div class="header-banner" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 24px; text-align: center;">
                <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff;">CTL Activity Report Dashboard</h1>
                <p style="margin: 8px 0 0; font-size: 13px; color: #94a3b8;">Department of ${isCe ? 'Computer Engineering' : 'Information Technology'}</p>
              </div>

              <div class="faculty-card" style="margin: 24px 24px 0 24px; padding: 18px; background-color: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                <table style="width: 100%; border-collapse: collapse;">
                  <tr>
                    <td style="vertical-align: middle; width: 44px;">
                      <div style="width: 40px; height: 40px; border-radius: 20px; background-color: #0f172a; color: #e5c185; text-align: center; line-height: 40px; font-weight: bold; font-size: 15px;">
                        ${(selectedFaculty ? selectedFaculty.initials : preparedBy.charAt(0)).toUpperCase()}
                      </div>
                    </td>
                    <td style="vertical-align: middle; padding-left: 12px;">
                      <div style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Prepared By</div>
                      <div style="font-size: 15px; font-weight: 700; color: #0f172a; margin-top: 2px;">${selectedFaculty ? selectedFaculty.name : preparedBy}</div>
                    </td>
                    <td style="vertical-align: middle; text-align: right;">
                      <div style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Generated On</div>
                      <div style="font-size: 13px; font-weight: 600; color: #0f172a; margin-top: 2px;">${new Date().toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                    </td>
                  </tr>
                </table>
              </div>

              <!-- Summary Metrics -->
              <div class="section-container" style="margin: 28px 24px 0 24px;">
                <h2 style="margin: 0 0 14px 0; font-size: 18px; font-weight: 700; color: #0f172a; border-left: 4px solid #c29a5b; padding-left: 10px;">Summary Metrics</h2>
                <table class="metrics-table" style="width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 0 -6px;">
                  <tr>
                    <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #64748b; border-radius: 8px; padding: 10px 4px; text-align: center;">
                      <div style="font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase;">Total</div>
                      <div style="font-size: 22px; font-weight: 800; color: #1e293b; margin-top: 2px;">${activeTotal}</div>
                    </td>
                    <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #3b82f6; border-radius: 8px; padding: 10px 4px; text-align: center;">
                      <div style="font-size: 9px; color: #3b82f6; font-weight: 700; text-transform: uppercase;">Submitted</div>
                      <div style="font-size: 22px; font-weight: 800; color: #1e40af; margin-top: 2px;">${activeSubmitted}</div>
                    </td>
                    <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #ef4444; border-radius: 8px; padding: 10px 4px; text-align: center;">
                      <div style="font-size: 9px; color: #ef4444; font-weight: 700; text-transform: uppercase;">Not Submitted</div>
                      <div style="font-size: 22px; font-weight: 800; color: #991b1b; margin-top: 2px;">${activeNotSubmitted}</div>
                    </td>
                    <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #10b981; border-radius: 8px; padding: 10px 4px; text-align: center;">
                      <div style="font-size: 9px; color: #10b981; font-weight: 700; text-transform: uppercase;">Approved</div>
                      <div style="font-size: 22px; font-weight: 800; color: #065f46; margin-top: 2px;">${activeApproved}</div>
                    </td>
                    <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #fbbf24; border-radius: 8px; padding: 10px 4px; text-align: center;">
                      <div style="font-size: 9px; color: #fbbf24; font-weight: 700; text-transform: uppercase;">Pending</div>
                      <div style="font-size: 22px; font-weight: 800; color: #92400e; margin-top: 2px;">${activePending}</div>
                    </td>
                    <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #991b1b; border-radius: 8px; padding: 10px 4px; text-align: center;">
                      <div style="font-size: 9px; color: #991b1b; font-weight: 700; text-transform: uppercase;">Reject</div>
                      <div style="font-size: 22px; font-weight: 800; color: #991b1b; margin-top: 2px;">${activeRejected}</div>
                    </td>
                  </tr>
                </table>
              </div>

              <!-- Activity Details -->
              <div style="margin: 32px 24px 36px 24px;">
                <h2 style="margin: 0 0 14px 0; font-size: 18px; font-weight: 700; color: #0f172a; border-left: 4px solid #c29a5b; padding-left: 10px;">Activity Details (${dataToExport.length} Activities)</h2>
                <div class="table-responsive" style="border: 1px solid #cbd5e1; border-radius: 12px; overflow-x: auto;">
                  <table style="width: 100%; min-width: 900px; border-collapse: collapse; text-align: left; font-size: 12px; border: 1px solid #cbd5e1;">
                    <thead>
                      <tr style="background-color: #f1f5f9;">
                        <th style="padding: 12px 14px; font-weight: 700; color: #334155; width: 5%; text-align: center; border: 1px solid #cbd5e1;">#</th>
                        <th style="padding: 12px 14px; font-weight: 700; color: #334155; width: 35%; border: 1px solid #cbd5e1;">Activity Name</th>
                        <th style="padding: 12px 14px; font-weight: 700; color: #334155; width: 14%; text-align: center; border: 1px solid #cbd5e1;">Plan Date</th>
                        <th style="padding: 12px 14px; font-weight: 700; color: #334155; width: 14%; text-align: center; border: 1px solid #cbd5e1;">Actual Date</th>
                        <th style="padding: 12px 14px; font-weight: 700; color: #334155; width: 20%; text-align: center; border: 1px solid #cbd5e1;">Submission Flags</th>
                        <th style="padding: 12px 14px; font-weight: 700; color: #334155; width: 12%; text-align: center; border: 1px solid #cbd5e1;">Approval</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${tableRowsHtml}
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Footer -->
              <div style="background-color: #f8fafc; padding: 20px 24px; border-top: 1px solid #cbd5e1; text-align: center;">
                <p style="margin: 0; font-size: 11.5px; color: #64748b;">Automated report generated by the <strong style="color: ${isCe ? '#2563eb' : '#c0392b'};">${isCe ? 'CE Department' : 'IT Department'}</strong> Portal.</p>
                <p style="margin: 4px 0 0; font-size: 10.5px; color: #94a3b8;">&copy; 2026 Department of Computer Engineering & Information Technology.</p>
              </div>
            </div>
          </div>
        </body>
        </html>
      `;

      const recipientEmail = document.getElementById('facultyEmail')?.value ||
        (localStorage.getItem('portal_dept') === 'CE' ? 'admincecse@gmiu.edu.in' : 'adminit@gmiu.edu.in');

      const emailSubject = 'CTL Activity Report In Website ERP Upload Documents';
      const sendUrl = (typeof window.APP_BASE_URL !== 'undefined' ? window.APP_BASE_URL : '') + 'send-email.php';

      fetch(sendUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          to: recipientEmail,
          cc: selectedCCEmails,
          subject: emailSubject,
          html: emailHtml,
          dept: isCe ? 'CE' : 'IT'
        })
      })
        .then(response => {
          return response.json().then(data => {
            if (!response.ok) {
              throw new Error(data.error || `HTTP error ${response.status}`);
            }
            return data;
          });
        })
        .then(data => {
          if (data.success) {
            sendBtn.innerHTML = `
              <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <span>Sent Successfully!</span>
            `;
            sendBtn.classList.remove('bg-[var(--color-primary)]');
            sendBtn.classList.add('bg-emerald-600');
            showToast('Activity Report sent successfully to ' + recipientEmail);
          } else {
            throw new Error(data.error || 'Server reported error');
          }
        })
        .catch(err => {
          console.error('Email send failed:', err);
          alert('Email dispatch failed: ' + err.message);

          sendBtn.innerHTML = `
            <svg class="w-4 h-4 text-rose-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            <span>Send Failed</span>
          `;
          sendBtn.classList.remove('bg-[var(--color-primary)]');
          sendBtn.classList.add('bg-rose-600');
        })
        .finally(() => {
          setTimeout(() => {
            sendBtn.disabled = false;
            sendBtn.classList.remove('bg-emerald-600', 'bg-rose-600');
            sendBtn.classList.add('bg-[var(--color-primary)]');
            sendBtn.innerHTML = `
              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M22 2L11 13"/><path d="M22 2L15 22l-4-9-9-4 20-7z"/>
              </svg>
              <span>Send Report</span>
            `;
          }, 3500);
        });
    });
  }

  // --- 9. DIRECT ERP QUICK ACCESS MODAL ---
  function initDirectErpModal() {
    const btn = document.getElementById('directErpBtn');
    const overlay = document.getElementById('erpModalOverlay');
    const closeBtn = document.getElementById('erpModalClose');
    const footerClose = document.getElementById('erpCloseBtn');
    const searchInput = document.getElementById('erpSearchInput');
    const searchClear = document.getElementById('erpSearchClear');
    const filterPills = document.querySelectorAll('#erpFilterPills .erp-pill');

    if (!btn || !overlay) return;

    const directErpData = (typeof facultyData !== 'undefined' ? facultyData : [])
      .filter(member => member.link && member.link.trim() !== '')
      .map(member => ({
        name: member.name,
        semClass: member.semClass || '',
        link: member.link,
        dept: member.dept || (member.department && member.department.toLowerCase().includes('computer') ? 'CE' : 'IT'),
        sem: member.sem || ''
      }));

    let activeErpFilter = 'all';
    let erpSearchQuery = '';

    function openModal() {
      overlay.classList.remove('hidden');
      overlay.classList.add('flex');
      document.body.style.overflow = 'hidden';
      if (searchInput) searchInput.focus();
      renderErpCards();
    }

    function closeModal() {
      overlay.classList.add('hidden');
      overlay.classList.remove('flex');
      document.body.style.overflow = '';
    }

    btn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (footerClose) footerClose.addEventListener('click', closeModal);

    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeModal();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !overlay.classList.contains('hidden')) {
        closeModal();
      }
    });

    if (searchInput) {
      searchInput.addEventListener('input', function () {
        erpSearchQuery = this.value.trim().toLowerCase();
        if (searchClear) searchClear.classList.toggle('hidden', erpSearchQuery.length === 0);
        renderErpCards();
      });
    }

    if (searchClear) {
      searchClear.addEventListener('click', function () {
        searchInput.value = '';
        erpSearchQuery = '';
        searchClear.classList.add('hidden');
        searchInput.focus();
        renderErpCards();
      });
    }

    filterPills.forEach(pill => {
      pill.addEventListener('click', function () {
        filterPills.forEach(p => p.classList.remove('active', 'bg-[var(--color-primary)]', 'text-white'));
        this.classList.add('active', 'bg-[var(--color-primary)]', 'text-white');
        activeErpFilter = this.getAttribute('data-filter') || 'all';
        renderErpCards();
      });
    });

    function renderErpCards() {
      const grid = document.getElementById('erpCardsGrid');
      const itemCount = document.getElementById('erpItemCount');
      if (!grid) return;

      const filtered = directErpData.filter(item => {
        let matchesFilter = true;
        if (activeErpFilter === 'ce') matchesFilter = item.dept.toUpperCase() === 'CE';
        else if (activeErpFilter === 'it') matchesFilter = item.dept.toUpperCase() === 'IT';
        else if (activeErpFilter.startsWith('sem')) matchesFilter = item.sem.toLowerCase() === activeErpFilter.toLowerCase();

        let matchesSearch = true;
        if (erpSearchQuery) {
          matchesSearch = item.name.toLowerCase().includes(erpSearchQuery) ||
            item.semClass.toLowerCase().includes(erpSearchQuery) ||
            item.dept.toLowerCase().includes(erpSearchQuery);
        }

        return matchesFilter && matchesSearch;
      });

      if (itemCount) {
        itemCount.textContent = `${filtered.length} link${filtered.length === 1 ? '' : 's'} available`;
      }

      if (filtered.length === 0) {
        grid.innerHTML = `
          <div class="col-span-full p-8 text-center text-xs text-[var(--text-muted)] flex flex-col items-center gap-2">
            <svg class="w-8 h-8 opacity-40 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <p>No ERP links found matching your criteria.</p>
          </div>
        `;
        return;
      }

      grid.innerHTML = filtered.map(item => `
        <div class="glass-card p-3 rounded-2xl flex flex-col justify-between gap-2.5 border border-white/80 dark:border-white/10 shadow-sm hover:border-[var(--color-primary)]/40 transition">
          <div>
            <div class="text-xs font-bold text-[var(--text-primary)] leading-tight truncate">${item.name}</div>
            <div class="text-[10.5px] text-[var(--text-muted)] truncate mt-0.5">${item.semClass || (item.dept + ' Department')}</div>
          </div>
          <a href="${item.link}" target="_blank" rel="noopener noreferrer" class="h-7 px-3 rounded-xl bg-[var(--color-primary)] text-white text-[10.5px] font-bold flex items-center justify-center gap-1.5 hover:brightness-105 active:scale-95 transition shadow-sm">
            <span>Open ERP Folder</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
              <polyline points="15 3 21 3 21 9"></polyline>
              <line x1="10" y1="14" x2="21" y2="3"></line>
            </svg>
          </a>
        </div>
      `).join('');
    }
  }

  // --- 10. INITIALIZATION RUNNER ---
  document.addEventListener('DOMContentLoaded', () => {
    initExcelUploader();
    initFacultySearch();
    initCCEmailsSearch();
    initEmailDispatch();
    initDirectErpModal();

    // Attach filter listeners
    const statusFilter = document.getElementById('statusFilter');
    const submissionFilter = document.getElementById('submissionFilter');
    const searchInput = document.getElementById('search');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');

    if (statusFilter) statusFilter.addEventListener('change', filterData);
    if (submissionFilter) submissionFilter.addEventListener('change', filterData);
    if (searchInput) searchInput.addEventListener('input', filterData);

    // Synchronized Top Navbar Search Listener
    window.addEventListener('portal:search', (e) => {
      const q = e.detail?.query || '';
      if (searchInput) searchInput.value = q;
      filterData();
    });

    if (resetFiltersBtn) {
      resetFiltersBtn.addEventListener('click', () => {
        if (statusFilter) statusFilter.value = '';
        if (submissionFilter) submissionFilter.value = '';
        if (searchInput) searchInput.value = '';
        filterData();
        showToast('Table filters cleared');
      });
    }

    // Initial table render (empty awaiting upload state)
    renderTable([]);
  });

})();

