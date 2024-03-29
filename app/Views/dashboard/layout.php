<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="<?= base_url('assets/base'); ?>/img/favicon.png" rel="icon">
    <title><?= SITE_NAME; ?> - <?= $title; ?></title>
    <link href="<?= base_url('assets/dashboard/'); ?>/css/ruang-admin.css" rel="stylesheet">
    <link href="<?= base_url('assets/dashboard/'); ?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url('assets/dashboard/'); ?>/vendor/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
    
    <link href="<?= base_url('assets/dashboard/'); ?>/vendor/datatables/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url() ?>/assets/base/css/croppie.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>/assets/base/css/pikaday.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="<?= base_url() ?>/assets/base/js/moment-with-locales.js"></script>
    <script src="<?= base_url('assets/dashboard'); ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url('assets/dashboard'); ?>/vendor/crypto-js-4.1.1/crypto-js.js"></script>
    <script src="<?= base_url('assets/dashboard'); ?>/js/Encryption.js"></script>

    <link href="<?= base_url('assets/dashboard'); ?>/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" >
    <script src="<?= base_url('assets/dashboard/'); ?>/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <!-- Bootstrap DatePicker -->  
        <!-- <link href="<?php echo base_url() ?>/assets/dashboard/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" >
        <script src="<?php echo base_url() ?>/assets/dashboard/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> -->

    
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" >
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script> -->

    <!-- ClockPicker -->
    <link href="<?php echo base_url() ?>/assets/dashboard/vendor/clock-picker/clockpicker.css" rel="stylesheet">
    <script src="<?php echo base_url() ?>/assets/dashboard/vendor/clock-picker/clockpicker.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/dashboard'); ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- chart.js -->
    <script src="<?= base_url('assets/dashboard'); ?>/vendor/chart.js/Chart.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script> -->
</head>
<div class="loading"></div> 
<style>

/* Absolute Center CSS Spinner */
.loading {
  position: fixed;
  z-index: 999;
  height: 2em;
  width: 2em;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}

/* Transparent Overlay */
.loading:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.3);
}

/* :not(:required) hides these rules from IE9 and below */
.loading:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

.loading:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 1em;
  height: 1em;
  margin-top: -0.5em;
  -webkit-animation: spinner 1500ms infinite linear;
  -moz-animation: spinner 1500ms infinite linear;
  -ms-animation: spinner 1500ms infinite linear;
  -o-animation: spinner 1500ms infinite linear;
  animation: spinner 1500ms infinite linear;
  border-radius: 0.5em;
  -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
  box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

</style>

<body id="page-top">

<div class="toast align-items-center text-white bg-danger toast-container position-absolute top-0 end-0 m-3" data-bs-animation="true" data-bs-autohide="true" data-bs-delay="3500" role="alert" aria-live="assertive" aria-atomic="true" id="toastError" style="z-index: 99999;background:#fc544bee !important;">
  <div class="d-flex">
    <div class="toast-body">
        <div class="card-body p-2">
            <span class="fs-6 text-white fw-bolder">Oops!</span>
            <div class="text-white-60 message-error fs-6"></div>
        </div>
    </div>
  </div>
</div>

<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('rekon'); ?>">
        <div style="font-size: 20px;" class="sidebar-brand-icon fw-lighter">R
                        <!-- <img src="http://localhost:8082/assets/base/img/logo2.png"> -->
        </div>
            <div class="sidebar-brand-text mx-3"><?= SITE_NAME; ?></div>
        </a>
        <hr class="sidebar-divider my-0">
       


        <?php if(isset($_SESSION['masukAdmin'])) { ?>

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/'); ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Home</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('/rekon'); ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Data Rekon</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('rekon_sch'); ?>">
                <i class="fas fa-calendar-check"></i>
                <span>Rekon Schedule</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('rekon_unmatch_bulanan'); ?>">
                <i class="fas fa-calendar-times"></i>
                <span>Rekon Unmatch Bulanan</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAddRekon" aria-expanded="true" aria-controls="collapseAddRekon">
                    <i class="fas fa-briefcase-medical "></i>
                    <span>Add Data Rekon</span></a>
                </a>
                <div id="collapseAddRekon" class="collapse dropdown-menu" aria-labelledby="headingBootstrap" data-bs-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">

                            <a class="collapse-item" href="<?= base_url('rekon/add'); ?>">Rekon</a>
                            <a class="collapse-item" href="<?= base_url('rekon_unmatch_bulanan/add'); ?>">Rekon Unmatch Bulanan</a>
                            <a class="collapse-item" href="<?= base_url('rekon_sch/add'); ?>">Rekon Sch</a>
                            <a class="collapse-item" href="<?= base_url('rekon_transaksi/add'); ?>">Rekon Transaksi</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseDataTransaksi" aria-expanded="true" aria-controls="collapseDataTransaksi">
                    <i class="fas fa-briefcase-medical "></i>
                    <span>Data Transaksi</span></a>
                </a>
                <div id="collapseDataTransaksi" class="collapse dropdown-menu" aria-labelledby="headingBootstrap" data-bs-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">

                            <a class="collapse-item" href="<?= base_url('data_transaksi'); ?>">Data Transaksi</a>
                            <a class="collapse-item" href="<?= base_url('data_transaksi/add'); ?>">Add Data Transaksi</a>
                    </div>
                </div>
            </li>

            <!-- <li class="nav-item">
                <a class="nav-link" href="<?= base_url('settlement/monit_disbursment'); ?>">
                <i class="fas fa-business-time"></i>
                <span>Monitoring Disbursement</span></a>
            </li> -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseDisbursement" aria-expanded="true" aria-controls="collapseDataTransaksi">
                    <i class="fas fas fa-business-time"></i>
                    <span>Disbursement</span></a>
                </a>
                <div id="collapseDisbursement" class="collapse dropdown-menu" aria-labelledby="headingBootstrap" data-bs-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="<?= base_url('settlement/monit_disbursment'); ?>">Monitoring Disbursement</a>
                            <a class="collapse-item" href="<?= base_url('settlement/order_disbursment'); ?>">Order Disbursement</a>
                            <!-- <a class="collapse-item" href="<?= base_url('settlement/detail_disbursment'); ?>">Detail Disbursement</a> -->
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('settlement'); ?>">
                <i class="fas fa-money-check-alt"></i>
                <span>Settlement</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('mitra'); ?>">
                <i class="fas fa-address-card"></i>
                <span>Data Mitra</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('mutasiSaldo'); ?>">
                <i class="fas fa-address-card"></i>
                <span>Mutasi Saldo</span></a>
            </li>

        <?php } ?>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBootstrap" aria-expanded="true" aria-controls="collapseBootstrap">
                <i class="fas fa-cogs"></i>
                <span>Setting</span></a>
            </a>
            <div id="collapseBootstrap" class="collapse dropdown-menu" aria-labelledby="headingBootstrap" data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">

                    <?php if(isset($_SESSION['masukAdmin'])) { ?>
                        <a class="collapse-item" href="<?= base_url('profil'); ?>">Profil</a>
                        <a class="collapse-item" href="<?= base_url('ftp'); ?>">FTP</a>
                        <a class="collapse-item" href="<?= base_url('database'); ?>">Database</a>
                        <a class="collapse-item" href="<?= base_url('mail'); ?>">Mail</a>
                    <?php } ?>
                    
                    <?php if(isset($_SESSION['masukMitra'])) { ?>
                        <a class="collapse-item" href="<?= base_url('profil'); ?>">Profil</a>
                    <?php } ?>
                    
                </div>
            </div>
        </li>
        <hr class="sidebar-divider">
        <div class="version" ><?= SITE_NAME ?> @ Version 1.0</div>
    </ul>
    <!-- Sidebar -->

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- TopBar -->
            <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
                <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3" style="color: #fff;">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ms-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="img-profile rounded-circle" src="<?= base_url('assets/dashboard'); ?>/img/boy.png" style="max-width: 60px">
                            <span class="ml-2 d-none d-lg-inline text-white small"><?= $_SESSION['uname'] ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="<?= base_url('profil') ?>">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profil
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= base_url('do_unauth') ?>">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- Topbar -->
            <?php

                function rupiah($angka){
                    if (!is_numeric($angka)) {
                        return '-';
                    }
                    
                    $hasil_rupiah = "Rp " . number_format($angka,0,',','.');
                    return $hasil_rupiah;
                
                }

            ?>

            <!-- DATA BODY -->
            <?php 
            echo view($view);
            ?>

        </div>
        
        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>copyright &copy; 2022 - <?= SITE_NAME; ?>
                    </span>
                </div>
            </div>
        </footer>
        <!-- Footer -->
    </div>
    
</div>

<!-- Scroll to top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>



<script src="<?= base_url('assets/dashboard'); ?>/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="<?= base_url('assets/dashboard'); ?>/vendor/datatables/datatables.min.js"></script>
<script src="<?= base_url('assets/dashboard'); ?>/js/ruang-admin.js"></script>

<!-- cart -->
<!-- <script src="<?= base_url('assets/dashboard'); ?>/vendor/chart.js/Chart.min.js"></script> -->
<script src="<?= base_url('assets/dashboard'); ?>/js/demo/chart-area-demo.js"></script>
<?php $isError = isset($_SESSION['error']) ? $_SESSION['error'] : ""; ?>
<!-- Page level custom scripts -->
<script>

$('.loading').hide();

    const isError = "<?= $isError ?>";
    if(isError != "") {
        var myToastEl = document.getElementById('toastError')
        var myToast = bootstrap.Toast.getOrCreateInstance(myToastEl) 
        myToast.show()
        $(".message-error").text(isError);
    }


 $(document).ready(function () {
      $('#dataTableMaster').DataTable({order: [[1, 'asc']]}); // ID From dataTable 
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTable2').DataTable(); // ID From dataTable
      $('#dataTable3').DataTable(); // ID From dataTable 
      $('#dataTable4').DataTable(); // ID From dataTable
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
</script>

</body>

</html>