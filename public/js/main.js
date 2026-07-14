const toggle = document.querySelector(".toggle");
const menu = document.querySelector(".nav-menu");

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
    toggle.addEventListener("click", toggleMenu, false);
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
