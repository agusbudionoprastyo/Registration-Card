document.addEventListener('DOMContentLoaded', function() {
    var regcard_pdf = document.getElementById('regcard_pdf');
    var regcardpdf = document.querySelector('#at_regform');
    
    var guestfolio_pdf = document.getElementById('guestfolio_pdf');
    var guestfoliopdf = document.querySelector('#at_guestfolio');
    
    var updateButton = document.getElementById('updateButton');
    var UploadButton = document.getElementById('UploadButton');
    // var folio = document.getElementById('folio');
    
    
    // Periksa apakah input hidden  tidak kosong
    if (guestfolio_pdf && guestfolio_pdf.value !== '') {
        var inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach(function(input) {
            input.setAttribute('readonly', true);
        });
        // Menonaktifkan input file jika  tidak kosong
        regcardpdf.disabled = true;
    }
    // Periksa apakah regcard_pdf kosong atau guestfolio_pdf tidak kosong
    if (regcard_pdf && regcard_pdf.value === ''||guestfolio_pdf && guestfolio_pdf.value !== '') {
        // Menonaktifkan input file jika kosong
        guestfoliopdf.disabled = true;
        UploadButton.disabled = true;
    }
    
    // Periksa apakah kedua input hidden memiliki nilai
    if (regcard_pdf && regcard_pdf.value !== '' && guestfolio_pdf && guestfolio_pdf.value !== '') {
        // Jika keduanya memiliki nilai, membuat semua input menjadi readonly
        var inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach(function(input) {
            input.setAttribute('readonly', true);
        });
        // Menonaktifkan tombol submit
        updateButton.disabled = true;
    }
    });
    
    // Skrip JavaScript untuk menyalin teks saat label diklik
    function copyText(inputId) {
      // Dapatkan teks dari elemen input dengan id yang sesuai
      var text = document.getElementById(inputId).value;
    
      // Salin teks ke clipboard
      navigator.clipboard.writeText(text);
    
      // Tampilkan pesan menggunakan iziToast
      iziToast.show({
        title: '<i class="fa-solid fa-clipboard-check"></i>',
        message: 'Teks berhasil disalin ke clipboard ' + text,
        position: 'topCenter',
        timeout: 3000,
        theme: 'light'
      });
    }
    
    document.getElementById('at_guestfolio').addEventListener('change', function(event) {
        const at_guestfolio = event.target;
        const fileInfo = document.getElementById('fileInfo');
    
        if (at_guestfolio.files.length > 0) {
            const file = at_guestfolio.files[0];
            const fileName = file.name;
            const fileSize = formatBytes(file.size);
    
            fileInfo.textContent = `File Name: ${fileName}, Size: ${fileSize}`;
        } else {
            fileInfo.textContent = "No file selected";
        }
    });
    
    // Fungsi untuk mengonversi ukuran file menjadi format yang lebih mudah dibaca
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
    
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    
        const i = Math.floor(Math.log(bytes) / Math.log(k));
    
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
    
    
    // Upload GuestFolio
    document.getElementById('uploadfolio').addEventListener('submit', async function (event) {
        event.preventDefault();
    
        // Lakukan sesuatu ketika form berubah di sini
        console.log("Form telah berubah!");
        try {
            // Ambil data formulir PDF
            const formData = new FormData(this);
    
            // Kirim permintaan untuk mengunggah file PDF
            const response = await fetch('/GuestFolio', {
                method: 'POST',
                body: formData
            });
    
            if (!response.ok) {
                throw new Error('Failed to upload PDF file');
            }
    
            // Ambil nama file dari respons
            const data = await response.json();
            const fileName = data.fileName;
    
            // Dapatkan id dari formulir HTML
            const id = document.getElementById('id').value;
    
            // Sekarang Anda dapat menggunakan nama file dan id untuk melakukan tindakan selanjutnya,
            // seperti mengirimnya ke server untuk diperbarui di database.
            updateGuestFolio(fileName, id);
        } catch (error) {
            console.error(error);
            document.getElementById('result').textContent = 'An error occurred while processing the files.';
        }
    });
    
    async function updateGuestFolio(fileName, id) {
      try {
        const response = await fetch('guestfolio_update.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ fileName: fileName, id: id })
        });
    
        if (response.ok) {
          // Mengarahkan pengguna ke halaman regform.php
          window.location.href = 'regform.php';
          console.log('PDF file name updated in the database');
        } else {
          console.error('Failed to update PDF file name in the database');
        }
      } catch (error) {
        console.error('Error updating PDF file name:', error);
      }
    }
    
    // Upload RegCard
    document.getElementById('uploadForm').addEventListener('submit', async function (event) {
        event.preventDefault();
        
        try {
            // Ambil data formulir PDF
            const formData = new FormData(this);
    
            // Kirim permintaan untuk mengunggah file PDF
            const response = await fetch('/upload', {
                method: 'POST',
                body: formData
            });
    
            if (!response.ok) {
                throw new Error('Failed to upload PDF file');
            }
    
            // Ambil nama file dari respons
            const data = await response.json();
            const fileName = data.fileName;
    
            // Dapatkan id dari formulir HTML
            const id = document.getElementById('id').value;
    
            // Sekarang Anda dapat menggunakan nama file dan id untuk melakukan tindakan selanjutnya,
            // seperti mengirimnya ke server untuk diperbarui di database.
            updatePDFFileName(fileName, id);
    
            // Setelah submit form uploadForm selesai, otomatis submit otherForm
            document.getElementById('data_all').submit();
        } catch (error) {
            console.error(error);
            document.getElementById('result').textContent = 'An error occurred while processing the files.';
        }
    });
    
    
    
    // Fungsi untuk mengirim nama file ke server untuk diperbarui di database
    async function updatePDFFileName(fileName, id) {
      try {
        const response = await fetch('regcard_update.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ fileName: fileName, id: id })
        });
    
        if (response.ok) {
          console.log('PDF file name updated in the database');
        } else {
          console.error('Failed to update PDF file name in the database');
        }
      } catch (error) {
        console.error('Error updating PDF file name:', error);
      }
    }
    
    // PDFocr
    document.addEventListener("DOMContentLoaded", async function() {
      let api = "https://script.google.com/macros/s/AKfycbw0FXMKiNe50TfVB7ZOh94J1jkPAXfuLhUzlvsCwBQt5eXgutoNNY-8itHyBVL8F7WUEQ/exec";
      let msg = document.querySelector(".message");
      let file = document.querySelector(".file");
      let inputTexts = document.querySelectorAll(".form-control");
      let loading = document.querySelector(".loading");
      let overlay = document.querySelector(".overlay"); // Reference to overlay element
      let pdfFileName = `RegCard_${Date.now()}`;
    
      file.addEventListener('change', () => {
        // Periksa apakah file telah dipilih
        if (!file.files[0]) {
          alert('Silakan pilih file terlebih dahulu.');
          return;
        }
    
        // Show overlay to prevent user interaction
        overlay.style.display = 'block';
        // Show loading animation
        loading.style.display = 'block';
    
        let fr = new FileReader();
        fr.readAsDataURL(file.files[0])
        fr.onload = () => {
          let res = fr.result;
          let b64 = res.split("base64,")[1];
          fetch(api, {
            method: "POST",
            body: JSON.stringify({
              file: b64,
              type: file.files[0].type,
              name: pdfFileName + '.pdf' // Menggunakan nilai dari elemen input dengan id "id"
            })
          })
          .then(res => res.text())
          .then(data => {
            // Hide overlay and loading animation after fetch is complete
            overlay.style.display = 'none';
            loading.style.display = 'none';
    
            let lines = data.split('\n');
            if (lines.length >= 16) {
              inputTexts[8].value = lines[2]; // Input 1
              inputTexts[7].value = lines[15].split(' ')[0]; // Input 2
              // Misalkan tanggal diterima dalam format "15-Apr-2024"
              let tanggalci = lines[6].split(' ')[0];
              let tanggalciObj = new Date(tanggalci); // Membuat objek Date dari tanggal yang diterima
    
              // Mengambil bagian-bagian tanggal yang diinginkan
              let gettahunci = tanggalciObj.getFullYear();
              let getbulanci = ("0" + (tanggalciObj.getMonth() + 1)).slice(-2); // Menambahkan 1 karena bulan dimulai dari 0
              let gettanggalci = ("0" + tanggalciObj.getDate()).slice(-2);
    
              // Menggabungkan bagian-bagian tanggal dengan tanda "-" di antaranya
              let civalue = `${gettahunci}-${getbulanci}-${gettanggalci}`;
    
              // Tempatkan nilai yang diinginkan ke dalam elemen input
              inputTexts[9].value = civalue;
    
              let tanggalco = lines[6].split(' ')[1];
              let tanggalcoObj = new Date(tanggalco); // Membuat objek Date dari tanggal yang diterima
    
              // Mengambil bagian-bagian tanggal yang diinginkan
              let gettahunco = tanggalcoObj.getFullYear();
              let getbulanco = ("0" + (tanggalcoObj.getMonth() + 1)).slice(-2); // Menambahkan 1 karena bulan dimulai dari 0
              let gettanggalco = ("0" + tanggalcoObj.getDate()).slice(-2);
    
              // Menggabungkan bagian-bagian tanggal dengan tanda "-" di antaranya
              let covalue = `${gettahunco}-${getbulanco}-${gettanggalco}`;
    
              // Tempatkan nilai yang diinginkan ke dalam elemen input
              inputTexts[10].value = covalue;
    
              // Tambahkan logika Anda di sini, misalnya menampilkan tanda tangan di halaman atau menyimpannya untuk digunakan nanti
              iziToast.show({
                title: 'Sukses',
                message: 'Registration Form berhasil diunggah dan telah di tanda tangani.',
                position: 'topCenter',
                timeout: 3000,
                theme: 'light'
              });
              } else {
              alert('Result does not contain enough lines.');
            }
          });
        }
      });
    });