<?php
//add this to avoid direct access to page
if ( count( get_included_files() ) == 1 ) {
    exit("Direct access not permitted.");
}
?>

<h1>Login page</h1>
<script>
    $('header').remove(); $('footer').remove();
</script>