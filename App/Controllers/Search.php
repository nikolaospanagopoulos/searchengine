<?php

namespace App\Controllers;

use App\Crawler;
use App\Models\AnalyticsModel;
use App\Models\ImageSearchModel;
use App\Models\SearchModel;
use Core\View;

class Search extends \Core\Controller
{



    public function crawlAction()
    {

        echo 'nikos';
        $crawler = new Crawler();
        $crawler->getAll('https://www.bbc.co.uk/', 2);
    }
    public function imagesSearchAction()
    {
        $pageNum = $this->route_params['id'] ?? 1;
        $numResults = ImageSearchModel::getNumResults($_GET['searchTerm']);
        $results = ImageSearchModel::getResultsHtml($pageNum, 30, $_GET['searchTerm']);

        $this->imageResultsAction($pageNum,$numResults,$results,20);
    }

    public function imageResultsAction($pageNum,$numResults,$results,$pageSize)
    {
        if (isset($_GET['sourceId'])) {
            AnalyticsModel::getUserData($_GET['searchTerm']);
        }

        View::render('Results/imageResults.php', [
            'results' => $results,
            'term' => $_GET['searchTerm'],
            'numResults' => $numResults,
            'pageNum' => $pageNum,
            'pageSize' => $pageSize
        ]);
    }
    public function searchAction()
    {


        $pageNum = $this->route_params['id'] ?? 1;
        $numResults = SearchModel::getNumResults($_GET['searchTerm']);
        $results = SearchModel::getResults($_GET['searchTerm'], $pageNum, 20);

        $this->resultsAction($results, $pageNum, 20, $numResults);
    }

    public function resultsAction($results, $pageNum, $pageSize, $numResults)
    {
        $numResults = SearchModel::getNumResults($_GET['searchTerm']);
        if (isset($_GET['sourceId'])) {
            AnalyticsModel::getUserData($_GET['searchTerm']);
        }

        View::render('Results/siteResults.php', [
            'results' => $results,
            'term' => $_GET['searchTerm'],
            'numResults' => $numResults,
            'pageNum' => $pageNum,
            'pageSize' => $pageSize
        ]);
    }

    public function updateClicks(){
        View::render('Results/updateClicks.php');
    }
}
