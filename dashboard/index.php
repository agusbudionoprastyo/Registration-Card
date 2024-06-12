<?php
require_once '../layout/_top.php';
require_once '../helper/connection.php';


$regcard = mysqli_query($connection, "SELECT COUNT(*) FROM regform where at_regform <> ''");

$total_regcard = mysqli_fetch_array($regcard)[0];

$guestfolio = mysqli_query($connection, "SELECT COUNT(*) FROM regform where at_guestfolio <> ''");

$total_guestfolio = mysqli_fetch_array($guestfolio)[0];

?>

<section class="section">
  <div class="section-header">
    <h1>Dashboard</h1>
  </div>
  <div class="column">
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
          <i class="fa-solid fa-file-pdf fa-2xl" style="color: #ffffff;"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Regcard</h4>
            </div>
            <div class="card-body">
              <?= $total_regcard ?>
            </div>
            </div>
          </div>
          <div class="card card-statistic-1">
          <div class="card-icon bg-info">
          <i class="fa-solid fa-file-pdf fa-2xl" style="color: #ffffff;"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Guestfolio</h4>
            </div>
            <div class="card-body">
              <?= $total_guestfolio ?>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

<?php
require_once '../layout/_bottom.php';
?>