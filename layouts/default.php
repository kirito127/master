<?php
//add this to avoid direct access to page
if ( count( get_included_files() ) == 1 ) {
    exit("Direct access not permitted.");
}
date_default_timezone_set('Asia/Manila');
$slug = $this->escape($this->slug);
//echo $slug;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->escape($this->pageTitle); ?></title>
    <link rel="stylesheet" href="./vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">
    <link rel="stylesheet" href="./css/style.css">

</head>
<body  class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden nano">
    <?= $this->partial('layouts/header.php'); ?>


    <div class="app-body">
        <?= $this->partial('layouts/admin_sidebar.php', array('myvar' => $slug)) ?>

        <main class="main">
            <?= $this->yieldView(); ?>
        </main>

    </div>

    <?= $this->partial('layouts/footer.php'); ?>

        <!-- <script src="./js/jquery-3.3.1.min.js"></script> -->
    <script src="../js/popover.min.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="../vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../js/pace.min.js"></script>
    <script src="../js/app.js"></script>
    <script src="../js/myscript.js"></script>
</body>
</html>