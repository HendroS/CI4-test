<?= $this->extend('layouts/auth_layout'); ?>

<?= $this->section('content') ?>
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 "><?= $title; ?></h1>
                                    <h5 class="mb-4"><?= session('reset_email'); ?></h5>
                                    <?= session()->getFlashdata('message'); ?>
                                </div>

                                <form action="<?= base_url('auth/changepassword') ?>" class="user" method="post">
                                    <?php csrf_field() ?>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Enter new password">
                                        <small class="text-danger pl-3">
                                            <?= validation_show_error('password') ?>
                                        </small>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user" id="confirm_password" name="confirm_password" placeholder="confirm password">
                                        <small class="text-danger pl-3">
                                            <?= validation_show_error('confirm_password') ?>
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Change Password
                                    </button>
                                </form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
<?= $this->endSection() ?>