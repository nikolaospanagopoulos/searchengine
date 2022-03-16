<?php

use App\Config;

include dirname(__DIR__) . '/header.php'; ?>

<div class="search-engine-container-results">
    <a href="<?php echo "/"   ?>">
        <img src="/images/logo.png" alt="website logo search" class="search-logo-results">
    </a>

    <form action="<?php echo  '/imagesearch/1'  ?>" method="GET">
        <input type="text" class="search-input" name="searchTerm" value="<?php echo $term; ?>">
        <button type="submit" class="search-button-results" name="sourceId" value="1">Search</button>
    </form>
</div>
<?php echo "<div class='num-results'>Results: " . $numResults . "</div>" ?>


<?php

if (strlen($term) == 0) {
    echo '<h1>No results available</h1>';
} else {
    echo $results;
}

?>

<?php

$pagesToShow = 10;

$numPages = ceil($numResults / $pageSize);

$pagesLeft = min($pagesToShow, $numPages);
$currentPage = $pageNum - floor($pagesToShow / 2);

if ($currentPage < 1) {
    $currentPage = 1;
}

$page = $pageNum;

if ($currentPage + $pagesLeft > $numPages + 1) {
    $currentPage = $numPages - $pagesLeft + 1;
}
echo '<div class="pagination-container">';
while ($pagesLeft != 0 && $currentPage <= $numPages) {
    if ($currentPage == $page) {
        echo "<div class='pageNumberContainer'>
            
            <span class='pageNumber'>" . $currentPage . "</span>
            </div>
            ";
    } else {
        echo "<div class='pageNumberContainer'>
                <a href=" .'/imagesearch/' . $currentPage . '?searchTerm=' . $term . ">
                
                <span class='pageNumber'>" . $currentPage . "</span>
                </a>
            </div>
            ";
    }
    $currentPage++;
    $pagesLeft--;
}
echo "</div>";


?>
<script src="/js/dist/src/validate.js"></script>
<script src="/js/dist/masonry.js"></script>


















<?php include dirname(__DIR__) . '/footer.php'; ?>