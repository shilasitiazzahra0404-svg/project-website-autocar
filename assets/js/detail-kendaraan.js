document.addEventListener("DOMContentLoaded", function () {

    const image = document.getElementById("vehicleImage");

    const colorText =
        document.getElementById("vehicleColor");

    const colorButtons =
        document.querySelectorAll(".color-option");


    if (
        !image ||
        !colorText ||
        colorButtons.length === 0
    ) {

        return;

    }


    let currentIndex = 0;

    let timer;


    /* =====================================================
       GANTI GAMBAR
    ===================================================== */

    function changeVehicle(index) {


        if (
            index < 0 ||
            index >= colorButtons.length
        ) {

            return;

        }


        const button =
            colorButtons[index];


        const newImage =
            button.getAttribute("data-image");


        const newColor =
            button.getAttribute("data-color");


        /* Animasi keluar */

        image.classList.add(
            "car-changing"
        );


        setTimeout(function () {


            /* Ganti gambar */

            image.src = newImage;


            /* Ganti tulisan warna */

            colorText.textContent =
                newColor;


            /* Ganti tombol aktif */

            colorButtons.forEach(
                function (btn) {

                    btn.classList.remove(
                        "active"
                    );

                }
            );


            button.classList.add(
                "active"
            );


            /* Animasi masuk */

            setTimeout(function () {

                image.classList.remove(
                    "car-changing"
                );

            }, 100);


        }, 450);


        currentIndex = index;

    }



    /* =====================================================
       GANTI OTOMATIS SETIAP 3,5 DETIK
    ===================================================== */

    function startAutoChange() {


        clearInterval(timer);


        timer = setInterval(function () {


            let nextIndex =
                currentIndex + 1;


            if (
                nextIndex >=
                colorButtons.length
            ) {

                nextIndex = 0;

            }


            changeVehicle(
                nextIndex
            );


        }, 3500);

    }



    /* =====================================================
       KETIKA TOMBOL WARNA DIKLIK
    ===================================================== */

    colorButtons.forEach(
        function (button) {


            button.addEventListener(
                "click",
                function () {


                    const index =
                        parseInt(
                            button.getAttribute(
                                "data-index"
                            )
                        );


                    changeVehicle(
                        index
                    );


                    /*
                    Setelah diklik,
                    otomatis tetap berjalan
                    */

                    startAutoChange();


                }
            );

        }
    );



    /* =====================================================
       MULAI OTOMATIS
    ===================================================== */

    startAutoChange();

});
