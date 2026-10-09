document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("tableSearch");
    const table = document.getElementById("dataTable");
    if (!input || !table) return;

    input.addEventListener("input", function () {
        const keyword = input.value.toLowerCase();
        Array.from(table.querySelectorAll("tr")).slice(1).forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(keyword) ? "" : "none";
        });
    });
});
