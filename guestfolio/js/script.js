document.addEventListener('DOMContentLoaded', function() {
    var deviceToken = localStorage.getItem('deviceTokenId') || 'default_token';
    let eventSource = new EventSource(`../update.php?device_token=${deviceToken}`);

    let lastId = null; // Variabel untuk menyimpan id terakhir yang diterima

    eventSource.onmessage = function(event) {
        const data = JSON.parse(event.data);

        // Hanya perbarui tampilan jika ID baru berbeda dari ID terakhir
        if (data.id !== lastId) {
            // Tampilkan notifikasi
            Swal.fire({
                icon: 'info',
                title: 'Guestfolio',
                text: `Guestfolio ${data.folio}, nama ${data.nama} siap untuk di tandatangani!`,
                showConfirmButton: false
            });

            // Update form fields with received data
            document.getElementById('id').value = data.id;
            document.getElementById('pdfFile').value = data.at_guestfolio;
            document.getElementById('folio').value = data.folio;

            // Langsung muat dan render PDF
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

            // Perbarui lastId dengan id baru
            lastId = data.id;
        }
    };

    eventSource.onerror = function(error) {
        console.error('EventSource failed:', error);
    };
});

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

        // Memanggil fungsi unpairDevice
        var tokenId = localStorage.getItem('deviceTokenId');
        if (tokenId) {
            unlinkDevice(tokenId);
        } else {
            console.error("No deviceTokenId found in localStorage.");
        }
    }
});

function unlinkDevice(tokenId) {
    $.ajax({
        url: 'unlinkDevice.php',
        type: 'POST',
        data: { token_id: tokenId, regform_id: '0' }, // Menambahkan status 'unpaired'
        success: function(response) {
            console.log("Doc unlinked");
        },
        error: function() {
            console.error("Failed to unlink doc");
        }
    });
}

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

// document.getElementById('pairing-btn').addEventListener('click', function() {
//     // Cek jika local storage sudah memiliki token_id
//     if (!localStorage.getItem('deviceTokenId')) {
//         // Jika tidak ada token_id, lakukan AJAX untuk pairing
//         $.ajax({
//             url: 'getToken.php',
//             type: 'GET',
//             success: function(response) {
//                 var data = JSON.parse(response); // Parse JSON response
//                 if (!data.error) {
//                     // Menyimpan token_id ke local storage
//                     localStorage.setItem('deviceTokenId', data.token_id);
//                     // alert("Token ID telah disimpan: " + data.token_id);
//                     Swal.fire({
//                         icon: 'success',
//                         title: 'Paired',
//                         text: 'Token ID saved ' + data.token_id,
//                         showConfirmButton: false
//                     }).then(() => {
//                         // Kirim permintaan untuk update status
//                         $.ajax({
//                             url: 'PairUnpairDevice.php',
//                             type: 'POST',
//                             data: { token_id: data.token_id, status: '1'},
//                             success: function(updateResponse) {
//                                 console.log("Status updated successfully");
//                                 // Reload halaman setelah status berhasil diupdate
//                                 location.reload();
//                             },
//                             error: function() {
//                                 console.error("Failed to update status");
//                             }
//                         });
//                     });
//                 } else {
//                     // alert("Error: " + data.error);
//                     Swal.fire({
//                         icon: 'error',
//                         title: 'Oops...',
//                         text: 'Error : ' + data.error,
//                         showConfirmButton: false
//                     });
//                 }
//             },
//             error: function() {
//                 alert("Error fetching data");
//             }
//         });
//     } else {
//         // Jika token_id sudah ada, beri notifikasi bahwa device sudah dipair
//         // alert("Device sudah dipair dengan token ID: " + localStorage.getItem('deviceTokenId'));
//         Swal.fire({
//             icon: 'warning',
//             title: 'Oops...',
//             text: 'Device already pair with token ID: ' + localStorage.getItem('deviceTokenId'),
//             showConfirmButton: false
//         });
//     }
//    });

document.getElementById('pairing-btn').addEventListener('click', function() {
    // Cek jika local storage sudah memiliki token_id
    if (!localStorage.getItem('deviceTokenId')) {
        // Jika tidak ada token_id, minta user untuk memasukkan token_id
        Swal.fire({
            title: 'Enter Token ID',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
                style: 'margin-bottom: 40px;' // Menambahkan margin bottom 40px
            },
            showConfirmButton: false,
            showCancelButton: false,
            showLoaderOnConfirm: true,
            preConfirm: (token_id) => {
                // Lakukan AJAX untuk memeriksa token_id
                return fetch(`getToken.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `token_id=${token_id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                // Menyimpan token_id ke local storage
                localStorage.setItem('deviceTokenId', result.value.token_id);
                Swal.fire({
                    icon: 'success',
                    title: 'Paired',
                    text: 'Token ID verified ' + result.value.token_id,
                    showConfirmButton: false
                }).then(() => {
                    // Kirim permintaan untuk update status
                    $.ajax({
                        url: 'PairUnpairDevice.php',
                        type: 'POST',
                        data: { token_id: result.value.token_id},
                        success: function(updateResponse) {
                            console.log("Status updated successfully");
                            // Reload halaman setelah status berhasil diupdate
                            location.reload();
                        },
                        error: function() {
                            console.error("Failed to update status");
                        }
                    });
                });
            }
        });
    } else {
        // Jika token_id sudah ada, beri notifikasi bahwa device sudah dipair
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Device already paired with token ID ' + localStorage.getItem('deviceTokenId'),
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
     title: 'Password',
     input: 'password',
     inputAttributes: {
         autocapitalize: 'off',
         autocorrect: 'off',
         style: 'margin-bottom: 40px;' // Menambahkan margin bottom 40px
     },
     showCancelButton: false,
     showConfirmButton: false,
     showLoaderOnConfirm: true,
     preConfirm: (password) => {
         if (password === "Dafam@188") {
             // Mengirim permintaan ke server untuk update status
             $.ajax({
                 url: 'PairUnpairDevice.php',
                 type: 'POST',
                 data: { token_id: tokenId}, // Menambahkan status 'unpaired'
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
                     }).then(() => {
                        // Reload halaman setelah notifikasi ditutup
                        location.reload();
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