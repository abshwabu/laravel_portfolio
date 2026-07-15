const toggle = document.querySelector(".toggle");
const menu = document.querySelector(".nav-menu");
const themeToggle = document.getElementById("theme-toggle");

function toggleMenu() {
    if (menu.classList.contains("active")) {
        menu.classList.remove("active");
        toggle.innerHTML = `<i class="fas fa-bars">`;
    } else {
        menu.classList.add("active");
        toggle.innerHTML = `<i class="fas fa-times"></i>`;
    }
}

if (toggle && menu) {
    toggle.setAttribute("role", "button");
    toggle.setAttribute("tabindex", "0");
    toggle.setAttribute("aria-label", "Toggle navigation menu");
    toggle.addEventListener("click", toggleMenu, false);
    toggle.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            toggleMenu();
        }
    });
}

if (themeToggle) {
    function updateThemeToggleState() {
        const isDark = document.documentElement.getAttribute("data-theme") === "dark";
        themeToggle.setAttribute("aria-pressed", isDark ? "true" : "false");
    }

    updateThemeToggleState();

    themeToggle.addEventListener("click", () => {
        const isDark = document.documentElement.getAttribute("data-theme") === "dark";
        const nextTheme = isDark ? "light" : "dark";

        document.documentElement.setAttribute("data-theme", nextTheme);
        localStorage.setItem("theme", nextTheme);
        updateThemeToggleState();
    });
}

const projectFilter = document.getElementById("project-filter");

if (projectFilter) {
    const filterPills = projectFilter.querySelectorAll(".filter-pill");
    const projectCards = document.querySelectorAll(".project-section .project-card");

    projectFilter.addEventListener("click", (event) => {
        const pill = event.target.closest(".filter-pill");
        if (!pill) {
            return;
        }

        filterPills.forEach((filterPill) => filterPill.classList.remove("is-active"));
        pill.classList.add("is-active");

        const selectedTag = pill.dataset.tag;

        projectCards.forEach((card) => {
            if (selectedTag === "all") {
                card.classList.remove("hidden");
                return;
            }

            const cardTags = card.dataset.tags ? card.dataset.tags.split(",") : [];

            if (cardTags.includes(selectedTag)) {
                card.classList.remove("hidden");
            } else {
                card.classList.add("hidden");
            }
        });
    });
}

const revealElements = document.querySelectorAll(".reveal");

if (revealElements.length > 0) {
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (prefersReducedMotion) {
        revealElements.forEach((element) => element.classList.add("is-visible"));
    } else {
        const revealObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.12,
                rootMargin: "0px 0px -5% 0px",
            }
        );

        revealElements.forEach((element) => revealObserver.observe(element));
    }
}
