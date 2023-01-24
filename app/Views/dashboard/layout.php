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
    <link href="<?= base_url('assets/dashboard/'); ?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url('assets/dashboard/'); ?>/vendor/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="<?= base_url('assets/dashboard/'); ?>/css/ruang-admin.css" rel="stylesheet">
    <link href="<?= base_url('assets/dashboard/'); ?>/vendor/datatables/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url() ?>/assets/base/css/croppie.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>/assets/base/css/pikaday.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="<?= base_url() ?>/assets/base/js/moment-with-locales.js"></script>
    <script src="<?= base_url('assets/dashboard'); ?>/vendor/jquery/jquery.min.js"></script>
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
</head>

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
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/'); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Data Rekon</span></a>
        </li>
        
        <?php if(!isset($_SESSION['masukAdmin'])) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('rekon/add'); ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Add Data Rekon</span></a>
            </li>
        <?php } ?>
        <li class="nav-item" hidden>
            <a class="nav-link" href="<?= base_url('rekon/rekon_sch'); ?>">
                <i class="fas fa-stopwatch"></i>
                <span>Rekon Schedule Master</span></a>
        </li>
        <?php if(isset($_SESSION['masukAdmin'])) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('mitra'); ?>">
                <i class="fas fa-address-card"></i>
                <span>Data Mitra</span></a>
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
                    <?php } ?>
                    
                    <?php if(isset($_SESSION['masukMitra'])) { ?>
                        <a class="collapse-item" href="<?= base_url('mitra/profil'); ?>">Profil</a>
                        <a class="collapse-item" href="<?= base_url('mitra/channel'); ?>">Channel</a>
                        <a class="collapse-item" href="<?= base_url('mitra/bank'); ?>">Bank</a>
                        <a class="collapse-item" href="<?= base_url('mitra/ftp'); ?>">FTP</a>
                        <a class="collapse-item" href="<?= base_url('mitra/database'); ?>">Database</a>
                        <a class="collapse-item" href="<?= base_url('mitra/profil'); ?>">Profil</a>
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
<script src="<?= base_url('assets/dashboard'); ?>/vendor/chart.js/Chart.min.js"></script>
<script src="<?= base_url('assets/dashboard'); ?>/js/demo/chart-area-demo.js"></script>
<?php $isError = isset($_SESSION['error']) ? $_SESSION['error'] : ""; ?>
<!-- Page level custom scripts -->
<script>

    const isError = "<?= $isError ?>";
    if(isError != "") {
        var myToastEl = document.getElementById('toastError')
        var myToast = bootstrap.Toast.getOrCreateInstance(myToastEl) 
        myToast.show()
        $(".message-error").text(isError);
    }


 $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTable2').DataTable(); // ID From dataTable
      $('#dataTable3').DataTable(); // ID From dataTable 
      $('#dataTable4').DataTable(); // ID From dataTable
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
</script>

</body>

</html>