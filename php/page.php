<?php
    include_once "php/imagekit-config.php";
    $page = json_decode(file_get_contents("json/pages.json"), true)[$page]??null;
?>
<section class="hero-image position-relative" style="background-image: url(<?php echo image($page["hero"]??null, "heros", 1320, 742); ?>)">
    <h1 class="mb-0 position-absolute top-50 start-50 translate-middle"><?php echo $page["text"]??null; ?></h1>
</section>
<p class="mt-3 mb-0 text-center"><?php echo $page["overview"]??null; ?></p>
<hr>