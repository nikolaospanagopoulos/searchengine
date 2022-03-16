<?php

use App\Config;

include dirname(__DIR__) . '/header.php'; ?>

<div id="images-link-container">
    <a href="<?php echo  '/imagessearch'  ?>">Images</a>
    <a href="<?php echo  '/analytics'  ?>">Analytics</a>
</div>

<div class="search-engine-container">
    <img src="/images/logo.png" alt="website logo search" class="search-logo">

    <form action="<?php echo  '/search/1'  ?>" method="GET" class="search-form">
        <input type="text" class="search-input" name="searchTerm">
        <button type="submit" class="search-button" name="sourceId" value="1">Search</button>
    </form>
</div>


<script src="/js/dist/src/validate.js"></script>

<?php include dirname(__DIR__) . '/footer.php'; ?>