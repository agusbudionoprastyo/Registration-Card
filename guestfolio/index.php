<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GuestFolio</title>
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" type="text/css" href="sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet"> 
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://kit.fontawesome.com/3595b79eb9.js" crossorigin="anonymous"></script>
        <style>
            /* Mengatur ukuran dan posisi kontainer halaman */
            #pdf-container {
                width: 100%; /* Menyesuaikan lebar dengan lebar container */
                height: 100%; /* Menyesuaikan tinggi dengan tinggi container */
                overflow: auto; /* Membuat konten yang melebihi ukuran kontainer dapat di-scroll */
                position: relative; /* Mengatur posisi relatif */
            }

            /* Mengatur gaya halaman PDF */
            .page {
                display: block; /* Menampilkan halaman sebagai blok */
                margin: 0 auto; /* Posisi halaman di tengah container */
                background-color: #fff; /* Warna latar belakang halaman */
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); /* Efek bayangan untuk halaman */
            }

            /* Mengatur ukuran dan posisi canvas untuk menampilkan halaman PDF */
            canvas {
                display: block; /* Menampilkan canvas sebagai blok */
                margin: 0 auto; /* Posisi canvas di tengah halaman */
                max-width: 100%; /* Maksimum lebar canvas */
                height: auto; /* Tinggi canvas disesuaikan secara otomatis */
            }
        </style>
    <script>

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('service-worker.js')
            .then(registration => {
            console.log('Service Worker registered with scope:', registration.scope);

            navigator.serviceWorker.addEventListener('message', event => {
                if (event.data.type === 'update') {
                alert(event.data.message);
                }
            });
            })
            .catch(error => {
            console.error('Service Worker registration failed:', error);
            });
    }

    function notifyServiceWorkerToUpdateCache() {
        if (navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({ type: 'clearCache' });
        }
    }
    </script>
</head>
<body>
    <form id="imageForm"> 
        <input type="hidden" id="device_id"/>
        <input type="hidden" id="pdfFile"/>
        <input type="hidden" id="folio"/>
        <div id="pdf-container"></div>
        <div id="signature-pad">
            <label><h3>SIGNATURE</h3></label>
            <canvas></canvas>
            <div class="input-group">
                <div class="input-wrapper">
                    <button type="button" class="undoClear" id="pairing-btn"><i class="fa-solid fa-arrows-rotate"></i></button>
                    <button type="button" class="undoClear" id="unpair-btn"><i class="fa-solid fa-ban"></i></button>
                    <button type="button" class="undoClear" data-action="clear"><i class="fa-solid fa-eraser"></i></button>
                    <button type="button" data-action="undo"><i class="fa-solid fa-rotate-left"></i></button>
                    <button type="button" id="save-btn" class="cyan">SUBMIT</button>
                </div>
            </div>
        </div>
    </form>
    
    <script src="sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="js/signature_pad.umd.js"></script>
    <script src="js/app.js"></script>
    <script src="js/axios.min.js"></script>
    <script src="js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.0.279/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.0.279/pdf.worker.min.js"></script>

    <script>
        // Ambil token dari local storage
        var deviceToken = localStorage.getItem('deviceTokenId');

        // Kirim token ke server menggunakan fetch API
        fetch('guestfolio/update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'device_token=' + deviceToken
        });

    document.getElementById('pairing-btn').addEventListener('click', function() {
        // Cek jika local storage sudah memiliki token_id
        if (!localStorage.getItem('deviceTokenId')) {
            // Jika tidak ada token_id, lakukan AJAX untuk pairing
            $.ajax({
                url: 'getUnpairedDevice.php',
                type: 'GET',
                success: function(response) {
                    var data = JSON.parse(response); // Parse JSON response
                    if (!data.error) {
                        // Menyimpan token_id ke local storage
                        localStorage.setItem('deviceTokenId', data.token_id);
                        // alert("Token ID telah disimpan: " + data.token_id);
                        Swal.fire({
                            icon: 'success',
                            title: 'Oops...',
                            text: 'Token ID saved ' + data.token_id,
                            showConfirmButton: false
                        });
                        // Kirim permintaan untuk update status
                        $.ajax({
                            url: 'updateDeviceStatus.php',
                            type: 'POST',
                            data: { token_id: data.token_id, status: '1'},
                            success: function(updateResponse) {
                                console.log("Status updated successfully");
                            },
                            error: function() {
                                console.error("Failed to update status");
                            }
                        });
                    } else {
                        // alert("Error: " + data.error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error : ' + data.error,
                            showConfirmButton: false
                        });
                    }
                },
                error: function() {
                    alert("Error fetching data");
                }
            });
        } else {
            // Jika token_id sudah ada, beri notifikasi bahwa device sudah dipair
            // alert("Device sudah dipair dengan token ID: " + localStorage.getItem('deviceTokenId'));
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Device already pair with token ID: ' + localStorage.getItem('deviceTokenId'),
                showConfirmButton: false
            });
        }
    });

    document.getElementById('unpair-btn').addEventListener('click', function() {
    var tokenId = localStorage.getItem('deviceTokenId');
    if (!tokenId) {
        // Menampilkan notifikasi jika tidak ada token ID
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No token ID saved',
            showConfirmButton: false
        });
    } else {
        Swal.fire({
            title: 'Enter your password',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: false,
            showConfirmButton: false,
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                if (password === "Dafam@188") {
                    // Mengirim permintaan ke server untuk update status
                    $.ajax({
                        url: 'updateDeviceStatus.php',
                        type: 'POST',
                        data: { token_id: tokenId, status: '0' }, // Menambahkan status 'unpaired'
                        success: function(response) {
                            console.log("Status updated successfully");
                            // Menghapus token_id dari local storage setelah berhasil update status
                            localStorage.removeItem('deviceTokenId');
                            // Menampilkan notifikasi bahwa device telah di-unpair
                            Swal.fire({
                                icon: 'info',
                                title: 'Unpaired',
                                text: 'Unpairing device success, token removed',
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            console.error("Failed to update status");
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Authentication failed',
                        text: 'Incorrect password',
                        showConfirmButton: false
                    });
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    }
});
    </script>
</body>
</html>
