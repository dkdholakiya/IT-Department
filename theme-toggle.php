<?php
/**
 * theme-toggle.php
 * Reusable Theme Toggle Component for CE & IT Portal
 * Provides client-side Dark & Light mode switching with localStorage persistence.
 * Includes silky-smooth global color, background, and shadow transition effects.
 * Default theme: Dark mode.
 */
?>
<!-- Immediate Early Script to prevent FOUC (Flash of Unstyled Content) -->
<script>
    (function() {
        const savedTheme = localStorage.getItem('gmiu_theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    })();
</script>

<!-- Floating Theme Switcher Button (Bottom Left Corner) -->
<div class="theme-switcher-container" id="themeSwitcherContainer">
    <button class="theme-toggle-btn" id="themeToggleBtn" aria-label="Toggle Light and Dark Theme" title="Switch Theme">
        <!-- Sun Icon (shown when in Dark Mode -> click to activate Light Mode) -->
        <svg class="theme-icon theme-icon-sun" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>

        <!-- Moon Icon (shown when in Light Mode -> click to activate Dark Mode) -->
        <svg class="theme-icon theme-icon-moon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </button>
</div>

<style>
/* Global smooth theme transition helper class */
html.theme-transitioning,
html.theme-transitioning *,
html.theme-transitioning *::before,
html.theme-transitioning *::after {
    transition: background-color 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                background-image 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                color 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                border-color 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                box-shadow 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                fill 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                stroke 0.45s cubic-bezier(0.4, 0, 0.2, 1) !important;
    transition-delay: 0s !important;
}

.theme-switcher-container {
    position: fixed;
    bottom: 24px;
    left: 24px;
    z-index: 99999;
}

.theme-toggle-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(11, 21, 48, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.16);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    color: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), 
                background 0.4s ease, 
                border-color 0.4s ease, 
                box-shadow 0.4s ease,
                color 0.4s ease;
    outline: none;
    padding: 0;
    overflow: hidden;
    position: relative;
}

/* Hover effects */
.theme-toggle-btn:hover {
    transform: translateY(-3px) scale(1.08);
    border-color: rgba(192, 57, 43, 0.5);
    box-shadow: 0 12px 30px rgba(192, 57, 43, 0.25), 0 4px 12px rgba(0, 0, 0, 0.4);
    color: #ffffff;
}

.theme-toggle-btn:active {
    transform: translateY(0) scale(0.92);
}

/* Icon animations */
.theme-icon {
    transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.35s ease;
}

.theme-toggle-btn:hover .theme-icon {
    transform: rotate(30deg) scale(1.1);
}

.theme-icon-sun {
    display: block;
}

.theme-icon-moon {
    display: none;
}

/* Light Theme styles for button */
[data-theme="light"] .theme-toggle-btn {
    background: rgba(255, 255, 255, 0.92);
    border-color: rgba(0, 0, 0, 0.12);
    color: #0f172a;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

[data-theme="light"] .theme-toggle-btn:hover {
    border-color: rgba(192, 57, 43, 0.4);
    box-shadow: 0 12px 30px rgba(192, 57, 43, 0.2), 0 4px 12px rgba(0, 0, 0, 0.1);
    color: #c0392b;
}

[data-theme="light"] .theme-icon-sun {
    display: none;
}

[data-theme="light"] .theme-icon-moon {
    display: block;
}

/* Spin animation on click */
.theme-icon-spin {
    animation: icon-spin-bounce 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes icon-spin-bounce {
    0% {
        transform: rotate(0deg) scale(0.7);
        opacity: 0.3;
    }
    50% {
        transform: rotate(180deg) scale(1.2);
    }
    100% {
        transform: rotate(360deg) scale(1);
        opacity: 1;
    }
}

/* Responsive adjustment */
@media (max-width: 599px) {
    .theme-switcher-container {
        bottom: 16px;
        left: 16px;
    }

    .theme-toggle-btn {
        width: 44px;
        height: 44px;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const themeBtn = document.getElementById("themeToggleBtn");
    if (!themeBtn) return;

    // Update tooltip title based on active theme
    function updateTooltip() {
        const currentTheme = document.documentElement.getAttribute("data-theme") || "light";
        if (currentTheme === "light") {
            themeBtn.setAttribute("title", "Switch to Dark Theme");
            themeBtn.setAttribute("aria-label", "Switch to Dark Theme");
        } else {
            themeBtn.setAttribute("title", "Switch to Light Theme");
            themeBtn.setAttribute("aria-label", "Switch to Light Theme");
        }
    }

    updateTooltip();

    let transitionTimer = null;

    themeBtn.addEventListener("click", function() {
        const currentTheme = document.documentElement.getAttribute("data-theme") || "light";
        const newTheme = (currentTheme === "dark") ? "light" : "dark";

        // Add smooth global transition class to root html element
        document.documentElement.classList.add("theme-transitioning");

        // Trigger icon spin animation
        const activeIcon = themeBtn.querySelector(newTheme === "light" ? ".theme-icon-moon" : ".theme-icon-sun");
        if (activeIcon) {
            activeIcon.classList.remove("theme-icon-spin");
            void activeIcon.offsetWidth; // Trigger reflow
            activeIcon.classList.add("theme-icon-spin");
        }

        // Apply new theme attribute
        document.documentElement.setAttribute("data-theme", newTheme);
        localStorage.setItem("gmiu_theme", newTheme);
        updateTooltip();

        // Clean up transition helper class after animation completes
        if (transitionTimer) clearTimeout(transitionTimer);
        transitionTimer = setTimeout(function() {
            document.documentElement.classList.remove("theme-transitioning");
        }, 460);
    });
});
</script>
