<?= $this->extend('layouts/auth_layout'); ?>

<?= $this->section('content'); ?>
<style>
    body {
        height: 100vh;
    }
</style>
<!-- Begin Page Content -->
<div class="container-fluid bg-white h-100">

    <!-- 404 Error Text -->
    <div class="text-center  justify-content-center d-flex align-items-center h-100">
        <div>
            <div class="error  m-auto" data-text="404">403</div>
            <p class="lead text-gray-800 mb-5">Access Blocked.</p>
            <p class="text-gray-500 mb-0">You are not allowed to access this page</p>
            <a href="<?= base_url('user'); ?>">&larr; Back to Dashboard</a>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
<?= $this->endSection(); ?>