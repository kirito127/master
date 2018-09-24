<?php
//add this to avoid direct access to page
if ( count( get_included_files() ) == 1 ) {
    exit("Direct access not permitted.");
}
?>

<header class="app-header navbar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
      <span class="navbar-toggler-icon">☰</span>
    </button>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
      <span class="navbar-toggler-icon">☰</span>
    </button>
    <ul class="nav navbar-nav d-md-down-none">
      <li class="nav-item px-3">
        <a class="nav-link" href="index.php">
          <h3>7deal Philippines</h3>
        </a>
      </li>
    </ul>
    <ul class="nav navbar-nav ml-auto mr-3">
      <li class="nav-item ">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="true">
          <i class="fa fa-bell"></i>
          <span class="badge badge-pill badge-danger" id="notif-count">0</span>
        </a>

        <!-- dropdown -->
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg" id="notif-container">
        </div>
        <!-- dropdown end -->
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
          aria-expanded="false">
          <img src="../img/default.png" class="img-avatar" alt="<?php echo 'test'; ?>">
          <span class="d-md-down-none">
            <?=isset($_SESSION['company']) ? $_SESSION['company'] : 'ADMIN'?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <div class="dropdown-header text-center">
            <strong>Account</strong>
          </div>
          <a class="dropdown-item" id="logoutbtn" href="#">
            <i class="fa fa-lock"></i> Logout</a>
        </div>
      </li>

    </ul>

  </header>