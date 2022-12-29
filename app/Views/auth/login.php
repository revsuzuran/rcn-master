<!-- Login Content -->
<div class="container-login d-flex align-items-center justify-content-center">
    <div class="row d-flex flex-column align-items-center">
            
            <div class="card shadow-sm mt-4" style='width:100%;max-width:400px'>
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="login-form">
                                <div class="d-flex justify-content-center mb-3">
                                    <img src="<?= base_url() ?>/assets/base/img/logo.png" height="80px">
                                </div>
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Login</h1>
                                </div>
                                <?php 
                                $session = session();
                                $errors = $session->getFlashdata('errors');
                                if($errors != null): ?>
                                    <div class="alert alert-danger" role="alert" id="ikierror">
                                        <span class="mb-0">
                                            <strong>Error!<strong> 
                                            <?php
                                                foreach($errors as $err){
                                                    echo $err;
                                                }
                                            ?>
                                        </span>
                                    </div>
                                <?php endif ?>
                                <form method="post" action="<?php echo base_url('do_auth'); ?>" class="user">
                                    <div class="form-group">
                                        <input type="text" class="form-control mt-2"  placeholder="Username" name="uname">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control mt-2" id="exampleInputPassword" placeholder="Password" name="password">
                                    </div>
                                    <!-- <div class="form-group">
                                        <div class="custom-control custom-checkbox small" style="line-height: 1.5rem;">
                                            <input type="checkbox" class="custom-control-input" id="customCheck">
                                            <label class="custom-control-label" for="customCheck">Remember
                                                Me</label>
                                        </div>
                                    </div> -->
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-danger btn-block">Login</button>
                                    </div>
                                </form>
                                <hr>
                                <div class="text-center">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
<!-- Login Content -->