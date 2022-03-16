<?php

namespace App\Controllers;

use \Core\View;


class Home extends \Core\Controller
{

    public function indexAction()
    {



        View::render('Home/index.php');
    }

    public function imagesAction()
    {



        View::render('Home/images.php');
    }

    public function resetDBAction(){
        View::render('CronJob/resetDB.php');
    }
}
