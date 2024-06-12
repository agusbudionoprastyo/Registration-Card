<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="../">
        <img src="/assets/image/Logo.png" alt="logo" width="100">
      </a>
    </div>

    <div class="sidebar-brand sidebar-brand-sm">
      <a href="../"><?= $_SESSION['login']['role'] ?></a>
    </div>

    <ul class="sidebar-menu">
      <li class="menu-header">Dashboard</li>
      <li><a class="nav-link" href="../"><i class="fa-solid fa-house-fire"></i> <span>Dashboard</span></a></li>
      <li class="menu-header">Fitur</li>

      <li class="dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
        <i class="fa-solid fa-fire"></i><span>Front Office</span>
        </a>
        <ul class="dropdown-menu">
          <?php if ($_SESSION['login']['role'] === 'admin' || $_SESSION['login']['role'] === 'user'): ?>
            <li><a class="nav-link" href="../roleadmin/regform.php"><i class="fa-solid fa-file-pdf fa-lg"></i> Regcard Guestfolio</a></li>
          <?php endif; ?>
        </ul>
      </li>

        <?php if ($_SESSION['login']['role'] === 'admin'): ?>
        <li class="dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
            <i class="fa-brands fa-slack fa-lg"></i><span>Audit</span>
                </a>
            <ul class="dropdown-menu">
        <li><a class="nav-link" href="../roleadmin/regform_guestfolio_audit.php"><i class="fa-solid fa-file-pdf fa-lg"></i> Regcard Guestfolio</a></li>
        <?php endif; ?>
        </ul>
      </li>
    </ul>
  </aside>
</div>
