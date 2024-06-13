let previousID = null;

// Set up EventSource for real-time updates
const eventSource = new EventSource('../update.php');

eventSource.onmessage = function(event) {
    const data = JSON.parse(event.data);

    // Check if id has changed
    if (data.id && (!previousID || previousID !== data.id)) {
        Swal.fire({
            icon: 'info',
            title: 'Guestfolio',
            text: `Guestfolio ${data.folio}, nama ${data.nama} siap untuk di tandatangani!`,
            showConfirmButton: false
        });
    }

    // Update form fields with received data
    document.getElementById('id').value = data.id; // Ganti 'device_token' dengan 'id'
    document.getElementById('pdfFile').value = data.at_guestfolio;
    document.getElementById('folio').value = data.folio;


    // Load and render PDF only if id or pdfFile has changed
    if (!previousID || previousID !== data.id || previousPdfFile !== data.at_guestfolio) {
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
    previousID = data.id; // Ganti 'device_token' dengan 'id'
};


eventSource.onerror = function(error) {
    console.error('EventSource failed:', error);
    // Handle the error as needed
};


document.getElementById('save-btn').addEventListener('click', function () {
    var id = document.getElementById('id').value; // Ganti 'device_token' dengan 'id'
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
        sendData(id, signatureData, pdfFile, folio); // Ganti 'device_token' dengan 'id'
    }
});

function sendData(id, signatureData, pdfFile, folio) { // Ganti 'device_token' dengan 'id'
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://card.dafam.cloud/g_sign_store.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    // Format the data to be sent
    var params = `id=${encodeURIComponent(id)}&signatureData=${encodeURIComponent(signatureData)}&pdfFile=${encodeURIComponent(pdfFile)}&folio=${encodeURIComponent(folio)}`; // Ganti 'device_token' dengan 'id'

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
    var formData =  '&id=' + encodeURIComponent(id) +  // Ganti 'device_token' dengan 'id'
                    '&signature=' + encodeURIComponent(signatureData) +
                    '&pdfFile=' + encodeURIComponent(pdfFile) + 
                    '&folio=' + encodeURIComponent(folio);

    // Mengirim data ke server
    xhr.send(formData);
}