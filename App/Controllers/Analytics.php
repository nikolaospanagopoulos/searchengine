<?php

namespace App\Controllers;

use App\Models\AnalyticsModel;
use \Core\View;


class Analytics extends \Core\Controller
{

    public function analyticsFormAction()
    {
        View::render('Analytics/analyticsForm.php');
    }
    public function indexAction()
    {
        AnalyticsModel::checkIfTermExists($_POST['searchTerm']);
        $data = AnalyticsModel::getAnalytics($_POST['searchTerm']);
        $cleanData = AnalyticsModel::cleanData($data);
        $this->show($cleanData);
    }
    public function showAction($data)
    {
        View::render('Analytics/sendAnalytics.php', [
            'data' => $data
        ]);
    }

    public function showUserAnalytics()
    {
        $data = AnalyticsModel::getUserAnalytics();
        $htmlData = AnalyticsModel::showUsersInfo($data);
        View::render('Analytics/userAnalytics.php', [
            'data' => $htmlData
        ]);
    }
}
