function parseKeywordTags(keywordString) {
    if (!keywordString) {
        return [];
    }

    return keywordString.split(",").map((tag) => tag.trim()).filter(Boolean);
}

const toggle = document.querySelector(".toggle");
const menu = document.querySelector(".nav-menu");
const themeToggle = document.getElementById("theme-toggle");

const barsIcon = `<svg class="icon" viewBox="0 0 24 24" width="1.25rem" height="1.25rem" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>`;
const timesIcon = `<svg class="icon" viewBox="0 0 24 24" width="1.25rem" height="1.25rem" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;

function toggleMenu() {
    const isExpanded = menu.classList.contains("active");
    if (isExpanded) {
        menu.classList.remove("active");
        toggle.innerHTML = barsIcon;
        toggle.setAttribute("aria-expanded", "false");
    } else {
        menu.classList.add("active");
        toggle.innerHTML = timesIcon;
        toggle.setAttribute("aria-expanded", "true");
    }
}

if (toggle && menu) {
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

        filterPills.forEach((filterPill) => {
            filterPill.classList.remove("is-active");
            filterPill.setAttribute("aria-pressed", "false");
        });
        pill.classList.add("is-active");
        pill.setAttribute("aria-pressed", "true");

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
    const modalSourceAvif = projectModal.querySelector(".modal-source-avif");
    const modalSourceWebp = projectModal.querySelector(".modal-source-webp");
    const galleryViewport = projectModal.querySelector(".modal-gallery-viewport");
    const galleryPrev = projectModal.querySelector(".gallery-prev");
    const galleryNext = projectModal.querySelector(".gallery-next");
    const galleryCounter = projectModal.querySelector(".gallery-counter");
    const modalThumbnails = projectModal.querySelector(".modal-thumbnails");
    const modalTitle = projectModal.querySelector(".modal-title");
    const modalTags = projectModal.querySelector(".modal-tags");
    const modalDescription = projectModal.querySelector(".modal-description");
    const modalLiveLink = projectModal.querySelector(".modal-button-live");
    const modalGithubLink = projectModal.querySelector(".modal-button-github");
    const modalCards = document.querySelectorAll('[role="button"][data-modal-title]');
    const focusableSelector = 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';

    let activeCard = null;
    let isModalOpen = false;
    let galleryImages = [];
    let currentGalleryIndex = 0;

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

    function setGalleryImage(index) {
        if (!galleryImages.length) {
            return;
        }

        currentGalleryIndex = (index + galleryImages.length) % galleryImages.length;
        const img = galleryImages[currentGalleryIndex];

        if (modalSourceAvif) {
            modalSourceAvif.srcset = img.avif_srcset || "";
        }
        if (modalSourceWebp) {
            modalSourceWebp.srcset = img.webp_srcset || "";
        }

        modalImage.src = img.src || "";
        modalImage.alt = img.alt || modalTitle.textContent || "Project preview";

        if (galleryCounter) {
            galleryCounter.textContent = `${currentGalleryIndex + 1} / ${galleryImages.length}`;
        }

        if (modalThumbnails) {
            const thumbs = modalThumbnails.querySelectorAll(".modal-thumb");
            thumbs.forEach((thumb, i) => {
                const isActive = i === currentGalleryIndex;
                thumb.classList.toggle("is-active", isActive);
                thumb.setAttribute("aria-selected", isActive ? "true" : "false");
                if (isActive) {
                    thumb.scrollIntoView({ behavior: "smooth", block: "nearest", inline: "nearest" });
                }
            });
        }
    }

    function openProjectModal(card) {
        activeCard = card;

        modalTitle.textContent = card.dataset.modalTitle || "";
        modalDescription.textContent = card.dataset.modalDescription || "";
        renderModalTags(card.dataset.modalTags || "");

        modalLiveLink.href = card.dataset.modalUrl || "#";
        modalGithubLink.href = card.dataset.modalGithub || "#";

        // Parse gallery images
        galleryImages = [];
        try {
            if (card.dataset.modalImages) {
                galleryImages = JSON.parse(card.dataset.modalImages);
            }
        } catch (e) {
            galleryImages = [];
        }

        if (!galleryImages.length) {
            const cardImg = card.querySelector("picture img, img");
            const fallbackSrc = cardImg && cardImg.currentSrc ? cardImg.currentSrc : getModalImageUrl(card.dataset.modalImage || "");
            if (fallbackSrc) {
                galleryImages = [{
                    src: fallbackSrc,
                    thumb: fallbackSrc,
                    avif_srcset: "",
                    webp_srcset: "",
                    alt: card.dataset.modalTitle || "Project preview"
                }];
            }
        }

        // Render thumbnails
        if (modalThumbnails) {
            modalThumbnails.innerHTML = "";
            if (galleryImages.length > 1) {
                galleryImages.forEach((img, i) => {
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "modal-thumb" + (i === 0 ? " is-active" : "");
                    btn.setAttribute("role", "tab");
                    btn.setAttribute("aria-label", `View screenshot ${i + 1}`);
                    btn.setAttribute("aria-selected", i === 0 ? "true" : "false");

                    const thumbImg = document.createElement("img");
                    thumbImg.src = img.thumb || img.src;
                    thumbImg.alt = img.alt || "";
                    thumbImg.width = 72;
                    thumbImg.height = 42;
                    thumbImg.loading = "lazy";
                    thumbImg.decoding = "async";

                    btn.appendChild(thumbImg);
                    btn.addEventListener("click", () => setGalleryImage(i));
                    modalThumbnails.appendChild(btn);
                });
                modalThumbnails.hidden = false;
            } else {
                modalThumbnails.hidden = true;
            }
        }

        const isMulti = galleryImages.length > 1;
        if (galleryPrev) galleryPrev.hidden = !isMulti;
        if (galleryNext) galleryNext.hidden = !isMulti;
        if (galleryCounter) galleryCounter.hidden = !isMulti;

        setGalleryImage(0);

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

        if (event.key === "ArrowLeft" && galleryImages.length > 1) {
            event.preventDefault();
            setGalleryImage(currentGalleryIndex - 1);
            return;
        }

        if (event.key === "ArrowRight" && galleryImages.length > 1) {
            event.preventDefault();
            setGalleryImage(currentGalleryIndex + 1);
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

    if (galleryPrev) {
        galleryPrev.addEventListener("click", () => setGalleryImage(currentGalleryIndex - 1));
    }
    if (galleryNext) {
        galleryNext.addEventListener("click", () => setGalleryImage(currentGalleryIndex + 1));
    }

    if (galleryViewport) {
        let touchStartX = 0;
        let touchEndX = 0;

        galleryViewport.addEventListener("touchstart", (e) => {
            if (e.changedTouches && e.changedTouches[0]) {
                touchStartX = e.changedTouches[0].screenX;
            }
        }, { passive: true });

        galleryViewport.addEventListener("touchend", (e) => {
            if (e.changedTouches && e.changedTouches[0]) {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 45) {
                    setGalleryImage(currentGalleryIndex + 1);
                } else if (touchEndX - touchStartX > 45) {
                    setGalleryImage(currentGalleryIndex - 1);
                }
            }
        }, { passive: true });
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
