document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[data-confirm]").forEach(function (element) {
        element.addEventListener("click", function (event) {
            const message = element.getAttribute("data-confirm");
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll("[data-modal-open]").forEach(function (button) {
        button.addEventListener("click", function () {
            const target = document.getElementById(button.dataset.modalOpen);
            if (target) target.classList.add("show");
        });
    });

    document.querySelectorAll("[data-modal-close]").forEach(function (button) {
        button.addEventListener("click", function () {
            const modal = button.closest(".modal");
            if (modal) modal.classList.remove("show");
        });
    });

    document.querySelectorAll(".modal").forEach(function (modal) {
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.classList.remove("show");
            }
        });
    });
    document.querySelectorAll("[data-height]").forEach(function (element) {
        const value = Number(element.dataset.height);
        element.style.height = Math.max(0, Math.min(100, value)) + "%";
    });

    document.querySelectorAll("[data-progress]").forEach(function (element) {
        const value = Number(element.dataset.progress);
        element.style.width = Math.max(0, Math.min(100, value)) + "%";
    });

});
