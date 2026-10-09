document.addEventListener("DOMContentLoaded", function () {
    const vehicle = document.getElementById("vehicleSelect");
    const price = document.getElementById("priceInput");

    function updatePrice() {
        if (!vehicle || !price) return;
        const option = vehicle.options[vehicle.selectedIndex];
        price.value = option ? option.dataset.price || "" : "";
    }

    if (vehicle) {
        vehicle.addEventListener("change", updatePrice);
        updatePrice();
    }
});
