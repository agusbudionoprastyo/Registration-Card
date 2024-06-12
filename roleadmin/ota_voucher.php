<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';

// Fetch data for displaying in the form
// Sanitize input
$id = isset($_GET['id']) ? mysqli_real_escape_string($connection, $_GET['id']) : '';

$query = mysqli_query($connection, "SELECT * FROM regform WHERE id='$id'");
$row = mysqli_fetch_array($query);
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
    <b>Upload Voucher</b>
    <a href="regform.php" class="btn btn-dark rounded-pill"><i class="fa-solid fa-chevron-left"></i></a>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="input-group mb-0">
           </div>
          <!-- Form -->
          <form action="ota_voucher_store.php" method="post" enctype="multipart/form-data">
              <table cellpadding="4" class="w-100">
                <tr><td><b for="">NAMA</b></td><td><b for="">ROOM</b></td></tr>
                <tr><td><i><?= $row['nama'] ?></i></td><td><i><?= $row['room'] ?></i></td></tr>
                <tr><td><b for="">FOLIO</b></td></tr>
                <tr><td><i><?= $row['folio'] ?></i></td></tr>
                <tr><td><b for="">CHECKIN</b></td><td><b for="">CHECKOUT</b></td></tr>
                <tr><td><i><?= $row['dateci'] ?></i></td><td><i><?= $row['dateco'] ?></i></td></tr>
                </table>
                <!-- File input and submit button -->
                <input type="hidden" id="id" name="id" value="<?= $row['id'] ?>"></input>
                <input type="hidden" id="folio" name="folio" value="<?= $row['folio'] ?>"></input>
                <input type="file" class="custom-file-input" id="voucher" name="voucher" accept="*" required />
                <span id="fileInfo"></span> <!-- Span to display file info -->
                <div class="btn-label-container">
                <label for="voucher" class="btn btn-dark rounded-pill"><i class="fa-solid fa-cloud-arrow-up fa-lg"></i> SELECT VOUCHER</label>
                <button class="btn btn-primary rounded-pill" type="submit"><i class="fa-solid fa-cloud"></i> UPLOAD</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>              
</section>

<script>
document.getElementById('voucher').addEventListener('change', function() {
    var fileInfo = document.getElementById('fileInfo');
    var file = this.files[0];
    if (file) {
        fileInfo.innerHTML = '<div><b>' + file.name + '</b></div><div>size: ' + formatBytes(file.size) + '</div>';
    } else {
        fileInfo.innerHTML = '';
    }
});

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
</script>

<?php
require_once '../layout/_bottom.php';
?>