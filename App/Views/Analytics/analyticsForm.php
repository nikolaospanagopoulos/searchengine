<?php

use App\Config;

include dirname(__DIR__) . '/header.php'; ?>

<div id="sites-link-container">
    <a href="<?php echo "/"    ?>">Sites</a>
    <a href="<?php echo  '/imagessearch'  ?>">Images</a>
</div>


<div class="analytics-form">
<h1>Monthly Analytics</h1>
    <h1>Search A Term</h1>
    <form action="<?php echo   '/analytics/index'  ?>" method="POST">
        <input type="text" name="searchTerm">
        <button class="term-analytics-button" type="submit">Get analytics</button>
    </form>
    <h1>User Analytics</h1>
    <form action="<?php echo   '/analytics/showUserAnalytics'  ?>" method="POST">
        <button class="user-analytics-button" type="submit">Get Users</button>
    </form>

</div>

<script src="/js/dist/src/validate.js"></script>



<?php include dirname(__DIR__) . '/footer.php'; ?>