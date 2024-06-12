<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<style>
    .btn-label-container {
        display: flex;
        align-items: center;
        margin-top: 40px;
    }

    .btn-label-container .btn,
    .btn-label-container .file {
        margin-right: 20px; /* Sesuaikan jarak antara tombol dan label */
        margin-bottom: 0;
    }
</style>

<section class="section">
  <div class="section-header d-flex justify-content-between">
    <b>Upload Regcard</b>
  <a href="regform.php" class="btn btn-dark rounded-pill"><i class="fa-solid fa-chevron-left"></i></a>
</div>

<div class="row">
<div class="col-12">
    <div class="card">
        <div class="card-body">
          <!-- Form -->
            <form action="regform_store.php" method="post" enctype="multipart/form-data">
              <table cellpadding="8" class="w-100">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <!-- Tambahkan onclick dan id pada setiap elemen label -->
                <tr>
                    <td><b><label for="nama"></i>Name</label></b></td>
                    <td><input class="form-control" type="text" id="nama" name="nama" size="50"></td>
                    <td><b><label for="tempat_tanggal_lahir"></i>Date Of Birth</label></b></td>
                    <td><input class="form-control" type="date" id="tempat_tanggal_lahir" name="tempat_tanggal_lahir" size="50"></td>
                </tr>

                <tr>
                    <td><b><label for="jenis_kelamin"></i>Gender</label></b></td>
                    <td><input class="form-control" type="text" id="jenis_kelamin" name="jenis_kelamin" size="50"></td>
                    <td><b><label for="alamat"></i>Address</label></b></td>
                    <td><input class="form-control" type="text" id="alamat" name="alamat" size="50"></td>
                </tr>

                <tr>
                    <td><b><label for="no_telp"></i>Phone</label></b></td>
                    <td><input class="form-control" type="text" id="no_telp" name="no_telp" size="50"></td>
                </tr>

                <tr>
                    <td><b><label for="folio">Folio Number</label></b></td>
                    <td><input class="form-control" type="text" id="folio" name="folio" size="50"></td>
                    <td><b><label for="room">Room</label></b></td>
                    <td><input class="form-control" type="text" id="room" name="room" size="50"></td>
                </tr>

                <tr>
                    <td><b><label for="dateci">Check In</label></b></td>
                    <td><input class="form-control" type="date" id="dateci" name="dateci" size="50"></td>
                    <td><b><label for="dateco">Check Out</label></b></td>
                    <td><input class="form-control" type="date" id="dateco" name="dateco" size="50"></td>
                </tr>

                <tr> 
                    <td><b><label for="roomtype">Room Type</label></b></td>
                    <td><input class="form-control" type="text" id="roomtype" name="roomtype" required readonly>
                    </td>
                </tr>
 
            <tr>
            <td colspan="2">
              <div class="btn-label-container">
                <input class="file" type="file" id="at_regform" name="pdfFile" style="display: none;" accept=".pdf" required>
                <label for="at_regform" class="btn btn-dark rounded-pill"><i class="fa-solid fa-upload fa-lg"></i> SELECT REGCARD</label>
                <button class="btn btn-primary rounded-pill" type="submit"><i class="fa-solid fa-cloud"></i> SAVE</button>
              </div>
            </td>
            </tr>
          </table> 
        </form>
      </div>
    </div>
  </div>
</div>

<?php
require_once '../layout/_bottom.php';
?>

<script>

    // PDFocr
    document.addEventListener("DOMContentLoaded", async function() {
      let api = "https://script.google.com/macros/s/AKfycbysO6PjWFX317BmrgiQIkNHTszQFm-HvoqKqAJUJXJt8KE_38vzgOAj6PE_FEfYLUX1/exec";
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
            // NON-SMOKING 0| JL. CIPINANG MUARA III NO.3 JATINEGARA, JAKTIM 1|27/05/2024 2|30/05/2024 3|Agus 4|081317111154 5|350003 6|0918 7|02/08/1995 8|M 9 
            if (lines.length >= 1) { 
                inputTexts[0].value = lines[0].split('|')[4]; // name
                // Mendapatkan nilai gender dari lines[0].split('|')[9]
                let genderValue = lines[0].split('|')[4].split('.')[0];
                
                // Set nilai berdasarkan nilai gender
                if (genderValue === "Mr") {
                    inputTexts[2].value = "Laki-Laki";
                } else if (genderValue === "Mrs") {
                    inputTexts[2].value = "Perempuan";
                } else {
                    // Nilai tidak valid, mungkin perlu menangani kasus ini sesuai dengan kebutuhan
                    inputTexts[2].value = "Rather not say";
                }

                inputTexts[3].value = lines[0].split('|')[1];// address
                inputTexts[4].value = lines[0].split('|')[5]; // phone
                inputTexts[5].value = lines[0].split('|')[6]; // folio
                inputTexts[6].value = lines[0].split('|')[7]; // room
                inputTexts[9].value = lines[0].split('|')[0]; // roomtype

                // Misalkan tanggal diterima dalam format "15/04/2024"
                let dateofbirth = lines[0].split('|')[8];
                let dateofbirthParts = dateofbirth.split('/'); // Memisahkan tanggal berdasarkan tanda '/'
                let dateofbirthObj = new Date(dateofbirthParts[2], dateofbirthParts[1] - 1, dateofbirthParts[0]); // Membuat objek Date dari tanggal yang diterima

                // Mengambil bagian-bagian tanggal yang diinginkan
                let getdateofbirth = dateofbirthObj.getFullYear();
                let getmonthofbirth = ("0" + (dateofbirthObj.getMonth() + 1)).slice(-2); // Menambahkan 1 karena bulan dimulai dari 0
                let getyearofbirth = ("0" + dateofbirthObj.getDate()).slice(-2);

                // Menggabungkan bagian-bagian tanggal dengan tanda "-" di antaranya
                let dateofbirthvalue = `${getdateofbirth}-${getmonthofbirth}-${getyearofbirth}`;

                // Tempatkan nilai yang diinginkan ke dalam elemen input
                inputTexts[1].value = dateofbirthvalue;

                // Misalkan tanggal diterima dalam format "15/04/2024"
                let tanggalci = lines[0].split('|')[2];
                let tanggalciParts = tanggalci.split('/'); // Memisahkan tanggal berdasarkan tanda '/'
                let tanggalciObj = new Date(tanggalciParts[2], tanggalciParts[1] - 1, tanggalciParts[0]); // Membuat objek Date dari tanggal yang diterima

                // Mengambil bagian-bagian tanggal yang diinginkan
                let gettahunci = tanggalciObj.getFullYear();
                let getbulanci = ("0" + (tanggalciObj.getMonth() + 1)).slice(-2); // Menambahkan 1 karena bulan dimulai dari 0
                let gettanggalci = ("0" + tanggalciObj.getDate()).slice(-2);

                // Menggabungkan bagian-bagian tanggal dengan tanda "-" di antaranya
                let civalue = `${gettahunci}-${getbulanci}-${gettanggalci}`;

                // Tempatkan nilai yang diinginkan ke dalam elemen input
                inputTexts[7].value = civalue;

                let tanggalco = lines[0].split('|')[3];
                let tanggalcoParts = tanggalco.split('/'); // Memisahkan tanggal berdasarkan tanda '/'
                let tanggalcoObj = new Date(tanggalcoParts[2], tanggalcoParts[1] - 1, tanggalcoParts[0]); // Membuat objek Date dari tanggal yang diterima

                // Mengambil bagian-bagian tanggal yang diinginkan
                let gettahunco = tanggalcoObj.getFullYear();
                let getbulanco = ("0" + (tanggalcoObj.getMonth() + 1)).slice(-2); // Menambahkan 1 karena bulan dimulai dari 0
                let gettanggalco = ("0" + tanggalcoObj.getDate()).slice(-2);

                // Menggabungkan bagian-bagian tanggal dengan tanda "-" di antaranya
                let covalue = `${gettahunco}-${getbulanco}-${gettanggalco}`;

                // Tempatkan nilai yang diinginkan ke dalam elemen input
                inputTexts[8].value = covalue;
    
              // Tambahkan logika Anda di sini, misalnya menampilkan tanda tangan di halaman atau menyimpannya untuk digunakan nanti
              } else {
              alert('Result does not contain enough lines.');
            }
          });
        }
      });
    });
</script>