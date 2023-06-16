<?= $this->extend('layouts/auth_layout'); ?>
<?= $this->section('content'); ?>

<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                <div class="col-lg-7">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                        </div>
                        <form class="user" action="<?= base_url('auth/register'); ?>" method="post">

                            <div class="form-group">
                                <input value="<?= old('name'); ?>" type="text" class="form-control form-control-user"
                                    id="name" name="name" placeholder="Full name">
                                <small class="text-danger pl-3">
                                    <?= validation_show_error('name') ?>
                                </small>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control form-control-user" id="email"
                                    placeholder="Email Address">
                                <small class="text-danger pl-3">
                                    <?= validation_show_error('email') ?>
                                </small>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="password" name="password" class="form-control form-control-user"
                                        id="password" placeholder="Password">
                                    <small class="text-danger pl-3">
                                        <?= validation_show_error('password') ?>
                                    </small>
                                </div>
                                <div class="col-sm-6">
                                    <input type="password" name="conf_password" class="form-control form-control-user"
                                        id="conf_password" placeholder="Repeat Password">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Register Account
                            </button>

                        </form>
                        <hr>
                        <div class="text-center">
                            <a class="small" href="<?= base_url('auth/forgotpassword'); ?>">Forgot Password?</a>
                        </div>
                        <div class="text-center">
                            <a class="small" href="<?= base_url('auth'); ?>">Already have an account? Login!</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>