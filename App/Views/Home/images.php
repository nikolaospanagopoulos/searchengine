<?php

use App\Config;

include dirname(__DIR__) . '/header.php'; ?>
<div id="sites-link-container">
    <a href="<?php echo "/"   ?>">Sites</a>
    <a href="<?php echo  '/analytics'  ?>">Analytics</a>
</div>

<div class="search-engine-container">
    <div style="display: flex;">
        <img src="/images/logo.png" alt="website logo search" class="search-logo">
        <h2>Images</h2>
    </div>

    <form action="<?php echo  '/imagesearch/1'  ?>" method="GET" class="search-form">
        <input type="text" class="search-input" name="searchTerm">
        <button type="submit" class="search-button" name="sourceId" value="1">Search</button>
    </form>
</div>


<script src="/js/dist/src/validate.js"></script>


<?php include dirname(__DIR__) . '/footer.php'; ?>