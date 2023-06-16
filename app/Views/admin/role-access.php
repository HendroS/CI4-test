<?= $this->extend('layouts/dashboard_layout'); ?>
<?= $this->section('content') ?>
<!-- Content Wrapper -->

<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">


        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto">

                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $user['name']; ?></span>
                        <img class="img-profile rounded-circle"
                            src="<?= base_url('assets/img/profile/') . $user['image'] ?>" />
                    </a>
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            My Profile
                        </a>

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?= basename('auth/logout'); ?>" data-toggle="modal"
                            data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>

            </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
            <div class="row">
                <?php helper('form'); ?>

                <div class="col-lg-6">

                    <?= session()->getFlashdata('message'); ?>

                    <h5><?= $role['role']; ?></h5>
                    <table class=" table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Access</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menu as $key => $m) : ?>
                            <tr>
                                <th scope="row"><?= $key + 1; ?></th>
                                <td><?= $m['menu']; ?></td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id=""
                                            <?= check_access($role['id'], $m['id']); ?> data-role="<?= $role['id']; ?>"
                                            data-menu="<?= $m['id']; ?>">
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>

                    </table>
                </div>
            </div>




        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->
    <script>
    //for change access
    $(document).ready(function() {
        $('.form-check-input').on('change', function() {
            const menuId = $(this).data('menu');
            const roleId = $(this).data('role');
            $.ajax({
                    url: "<?= base_url('admin/changeAccess'); ?>",
                    type: 'post',
                    data: {
                        menuId: menuId,
                        roleId: roleId,
                    },
                    success: function() {
                        document.location.href = "<?= base_url('admin/roleAccess/'); ?>" +
                            roleId;
                    },
                }

            );
        })
    });
    </script>


    <?= $this->endSection() ?>