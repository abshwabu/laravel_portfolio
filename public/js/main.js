function parseKeywordTags(keywordString) {
    if (!keywordString) {
        return [];
    }

    return keywordString.split(",").map((tag) => tag.trim()).filter(Boolean);
}

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

const projectModal = document.getElementById("project-modal");

if (projectModal) {
    const modalPanel = projectModal.querySelector(".modal-panel");
    const modalClose = projectModal.querySelector(".modal-close");
    const modalImage = projectModal.querySelector(".modal-image");
    const modalTitle = projectModal.querySelector(".modal-title");
    const modalTags = projectModal.querySelector(".modal-tags");
    const modalDescription = projectModal.querySelector(".modal-description");
    const modalLiveLink = projectModal.querySelector(".modal-button-live");
    const modalGithubLink = projectModal.querySelector(".modal-button-github");
    const modalCards = document.querySelectorAll('[role="button"][data-modal-title]');
    const focusableSelector = 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';

    let activeCard = null;
    let isModalOpen = false;

    function getModalImageUrl(imagePath) {
        if (!imagePath) {
            return "";
        }

        if (imagePath.startsWith("http://") || imagePath.startsWith("https://") || imagePath.startsWith("/")) {
            return imagePath;
        }

        return `/storage/${imagePath}`;
    }

    function getFocusableElements(container) {
        return Array.from(container.querySelectorAll(focusableSelector)).filter(
            (element) => !element.hasAttribute("disabled") && element.getAttribute("tabindex") !== "-1"
        );
    }

    function renderModalTags(tagString) {
        modalTags.innerHTML = "";

        parseKeywordTags(tagString).forEach((tag) => {
            const tagElement = document.createElement("span");
            tagElement.textContent = tag;
            modalTags.appendChild(tagElement);
        });
    }

    function openProjectModal(card) {
        activeCard = card;

        modalTitle.textContent = card.dataset.modalTitle || "";
        modalImage.src = getModalImageUrl(card.dataset.modalImage || "");
        modalImage.alt = card.dataset.modalTitle || "Project preview";
        modalDescription.textContent = card.dataset.modalDescription || "";
        renderModalTags(card.dataset.modalTags || "");

        modalLiveLink.href = card.dataset.modalUrl || "#";
        modalGithubLink.href = card.dataset.modalGithub || "#";

        projectModal.hidden = false;
        projectModal.classList.add("is-open");
        document.body.style.overflow = "hidden";
        isModalOpen = true;

        modalClose.focus();
    }

    function closeProjectModal() {
        if (!isModalOpen) {
            return;
        }

        projectModal.classList.remove("is-open");
        projectModal.hidden = true;
        document.body.style.overflow = "";
        isModalOpen = false;

        if (activeCard) {
            activeCard.focus();
            activeCard = null;
        }
    }

    function handleModalKeydown(event) {
        if (!isModalOpen) {
            return;
        }

        if (event.key === "Escape") {
            event.preventDefault();
            closeProjectModal();
            return;
        }

        if (event.key !== "Tab") {
            return;
        }

        const focusableElements = getFocusableElements(modalPanel);

        if (focusableElements.length === 0) {
            return;
        }

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey && document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
        } else if (!event.shiftKey && document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    }

    modalCards.forEach((card) => {
        card.addEventListener("click", (event) => {
            if (event.target.closest(".project-links")) {
                return;
            }

            openProjectModal(card);
        });

        card.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                openProjectModal(card);
            }
        });
    });

    modalClose.addEventListener("click", closeProjectModal);

    projectModal.addEventListener("click", (event) => {
        if (event.target === projectModal) {
            closeProjectModal();
        }
    });

    document.addEventListener("keydown", handleModalKeydown);
}
