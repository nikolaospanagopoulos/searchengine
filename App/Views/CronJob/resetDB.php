<?php

use App\Config;
use App\Models\AnalyticsModel;




$dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
$db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmt = 'TRUNCATE TABLE analytics;';



$query = $db->prepare($stmt);

$query->execute();

$stmt = 'TRUNCATE TABLE userAnalytics';



$query = $db->prepare($stmt);

$query->execute();
