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
    <b>Edit Regcard</b>
  <a href="regform.php" class="btn btn-dark rounded-pill"><i class="fa-solid fa-chevron-left"></i></a>
</div>

<div class="row">
<div class="col-12">
    <div class="card">
        <div class="card-body">
          <!-- Form -->
            <form action="regform_update.php" method="post" enctype="multipart/form-data">
              <table cellpadding="8" class="w-100">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <!-- Tambahkan onclick dan id pada setiap elemen label -->
                <tr>
                    <td><b><label for="nama"></i>Name</label></b></td>
                    <td><input class="form-control" type="text" id="nama" name="nama" size="50" value="<?= $row['nama'] ?>" readonly></td>
                    <td><b><label for="tempat_tanggal_lahir"></i>Date Of Birth</label></b></td>
                    <td><input class="form-control" type="date" id="tempat_tanggal_lahir" name="tempat_tanggal_lahir" size="50" value="<?= $row['tempat_tanggal_lahir'] ?>" readonly></td>
                </tr>

                <tr>
                    <td><b><label for="jenis_kelamin"></i>Gender</label></b></td>
                    <td><input class="form-control" type="text" id="jenis_kelamin" name="jenis_kelamin" size="50" value="<?= $row['jenis_kelamin'] ?>" readonly></td>
                    <td><b><label for="alamat"></i>Address</label></b></td>
                    <td><input class="form-control" type="text" id="alamat" name="alamat" size="50" value="<?= $row['alamat'] ?>" readonly></td>
                </tr>

                <tr>
                    <td><b><label for="no_telp"></i>Phone</label></b></td>
                    <td><input class="form-control" type="text" id="no_telp" name="no_telp" size="50" value="<?= $row['no_telp'] ?>" readonly></td>
                </tr>

                <tr>
                    <td><b><label for="folio">Folio Number</label></b></td>
                    <td><input class="form-control" type="text" id="folio" name="folio" size="50" value="<?= $row['folio'] ?>" readonly></td>
                    <td><b><label for="room">Room</label></b></td>
                    <td><input class="form-control" type="text" id="room" name="room" size="50" value="<?= $row['room'] ?>"></td>
                </tr>

                <tr>
                    <td><b><label for="dateci">Check In</label></b></td>
                    <td><input class="form-control" type="date" id="dateci" name="dateci" size="50" value="<?= $row['dateci'] ?>" readonly></td>
                    <td><b><label for="dateco">Check Out</label></b></td>
                    <td><input class="form-control" type="date" id="dateco" name="dateco" size="50" value="<?= $row['dateco'] ?>" readonly></td>
                </tr>

                <tr> 
                    <td><b><label for="roomtype">Room Type</label></b></td>
                    <td><input class="form-control" type="text" id="roomtype" name="roomtype" value="<?= $row['roomtype'] ?>" readonly>
                    </td>
                </tr>
            <tr>
            <td colspan="2">
              <div class="btn-label-container">
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