<?php
//add this to avoid direct access to page
if ( count( get_included_files() ) == 1 ) {
    exit("Direct access not permitted.");
}
$slug =  $this->sharedData()->get('slug');
?>
<div class="sidebar">
    <nav class="sidebar-nav">

            <ul class="nav">
                <li class="nav-title">menu</li>

                <li class='nav-item'>
                    <a class='nav-link  <?= in_array($slug, array('/', '')) ? 'active': null ?>' href='/'>
                        <i class='fas fa-tachometer-alt'></i> Dashboard
                    </a>
                </li>

                <li class='nav-item'>
                    <a class='nav-link  <?= in_array($slug, array('/product', '/product')) ? 'active': null ?>' href='/product'>
                        <i class='fab fa-product-hunt'></i> Products
                    </a>
                </li>

            </ul>

        </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>