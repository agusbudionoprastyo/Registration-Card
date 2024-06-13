let previousDeviceToken = null;

// Set up EventSource for real-time updates
const eventSource = new EventSource('../update.php');

eventSource.onmessage = function(event) {
    const data = JSON.parse(event.data);

    // Check if device_token has changed
    if (data.device_token && (!previousDeviceToken || previousDeviceToken !== data.device_token)) {
        Swal.fire({
            icon: 'info',
            title: 'Guestfolio',
            text: `Guestfolio ${data.folio}, nama ${data.nama} siap untuk di tandatangani!`,
            showConfirmButton: false
        });
    }

    // Update form fields with received data
    document.getElementById('device_token').value = data.device_token;
    document.getElementById('pdfFile').value = data.at_guestfolio;
    document.getElementById('folio').value = data.folio;


    // Load and render PDF only if device_token or pdfFile has changed
    if (!previousDeviceToken || previousDeviceToken !== data.device_token || previousPdfFile !== data.at_guestfolio) {
        const pdfUrl = data.at_guestfolio;
        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        loadingTask.promise.then(function(pdf) {
            console.log('PDF loaded');
            // Ambil halaman pertama PDF
            pdf.getPage(1).then(function(page) {
                console.log('Page loaded');
                const scale = 1;
                const viewport = page.getViewport({scale: scale});
                // Buat canvas untuk menampilkan halaman PDF
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                // Menggambar halaman PDF ke dalam canvas
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                const renderTask = page.render(renderContext);
                renderTask.promise.then(function() {
                    console.log('Page rendered');
                    // Hapus konten lama dari pdf-viewer
                    const pdfViewer = document.getElementById('pdf-container');
                    pdfViewer.innerHTML = '';
                    // Menambahkan canvas ke dalam div
                    pdfViewer.appendChild(canvas);
                });
            });
        }).catch(function(reason) {
            // Jika gagal memuat PDF
            console.error('Error: ' + reason);
        });

        // Update the previous pdfFile
        previousPdfFile = data.at_guestfolio;
    }

    // Update the previous device ID
    previousDeviceToken = data.device_token;
};


eventSource.onerror = function(error) {
    console.error('EventSource failed:', error);
    // Handle the error as needed
};


document.getElementById('save-btn').addEventListener('click', function () {
    var device_token = document.getElementById('device_token').value;
    var pdfFile = document.getElementById('pdfFile').value;
    var folio = document.getElementById('folio').value;

    if (signaturePad.isEmpty()) {
        // Memeriksa apakah tanda tangan kosong
        Swal.fire({
            iconHtml: '<i class="fa-solid fa-signature fa-2xs" style="color: #ff2b85;"></i>',
            title: 'Oops...',
            text: 'Silakan tandatangani terlebih dahulu!',
            showConfirmButton: false
        });
    } else {
        // Mendapatkan data tanda tangan
        var signatureData = signaturePad.toDataURL();
        
        // Mengirim data ke server
        sendData(device_token, signatureData, pdfFile, folio);
    }
});

function sendData(device_token, signatureData, pdfFile, folio) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://card.dafam.cloud/g_sign_store.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    // Format the data to be sent
    var params = `device_token=${encodeURIComponent(device_token)}&signatureData=${encodeURIComponent(signatureData)}&pdfFile=${encodeURIComponent(pdfFile)}&folio=${encodeURIComponent(folio)}`;

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'OK...',
                    text: 'Guestfolio berhasil ditandatangani',
                    showConfirmButton: false,
                    timer: 3000 // Display the alert for 2 seconds
                }).then(() => {
                    // Reload the page after the alert is closed
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error uploading data.',
                    showConfirmButton: false
                }).then(() => {
                    // Reload the page even after an error, if desired
                    location.reload();
                });
            }
        }
    };
    
    // Membuat string data yang akan dikirim
    var formData =  '&device_token=' + encodeURIComponent(device_token) + 
                    '&signature=' + encodeURIComponent(signatureData) +
                    '&pdfFile=' + encodeURIComponent(pdfFile) + 
                    '&folio=' + encodeURIComponent(folio);

    // Mengirim data ke server
    xhr.send(formData);
}