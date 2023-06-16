 <!-- Sidebar -->
 <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

     <!-- Sidebar - Brand -->
     <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
         <div class="sidebar-brand-icon rotate-n-15">
             <i class="fa fa-star" aria-hidden="true"></i>


         </div>

         <div class="sidebar-brand-text mx-3">My Admin</div>
     </a>

     <!-- Divider -->
     <hr class="sidebar-divider">

     <!-- loop menu -->
     <?php

        use App\Models\UserMenu;

        foreach ($menu as $m) : ?>
     <!-- Heading -->
     <div class="sidebar-heading">
         <?= $m['menu'] ?>
     </div>

     <!-- loop sub menu -->
     <?php
            $subMenu = (new UserMenu())->getSubMenu($m['id'])
            ?>
     <?php foreach ($subMenu as $sub) : ?>

     <li class="nav-item <?= $sub['title'] == $title ? 'active' : ''; ?>">
         <a class="nav-link pb-0" href="<?= base_url($sub['url']); ?>">
             <i class="<?= $sub['icon']; ?>"></i>
             <span><?= $sub['title']; ?></span></a>
     </li>


     <?php endforeach ?>
     <hr class="sidebar-divider d-none d-md-block mt-3">
     <?php endforeach ?>

     <li class="nav-item">
         <a data-toggle="modal" data-target="#logoutModal" class="nav-link" href="<?= base_url('auth/logout'); ?>">
             <i class="fas fa-fw fa-arrow-right"></i>
             <span>Log out</span>
         </a>
     </li>
     <hr class="sidebar-divider d-none d-md-block">

     <!-- Sidebar Toggler (Sidebar) -->
     <div class="text-center d-none d-md-inline">
         <button class="rounded-circle border-0" id="sidebarToggle"></button>
     </div>

 </ul>
 <!-- End of Sidebar -->