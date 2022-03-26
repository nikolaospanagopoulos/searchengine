<?php

use App\Config;

include dirname(__DIR__) . '/header.php'; ?>

<?php  

echo "<h1 class='analytics-title'>User Analytics</h1>";

foreach($data as $html){
    echo $html;
}

?>




<?php include dirname(__DIR__) . '/footer.php'; ?>