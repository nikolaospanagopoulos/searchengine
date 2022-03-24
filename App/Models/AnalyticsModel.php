<?php

namespace App\Models;

use PDO;
use stdClass;

class AnalyticsModel extends \Core\Model
{
    public static function getOperatingSystem($client)
    {
        $operatingSystem = explode(";", $client)[1];
        $operatingSystem = explode(')', $operatingSystem)[0];

        return $operatingSystem;
    }

    public static function getBrowser($client)
    {
        $ub = '';

        if (preg_match('/MSIE/i', $client) && !preg_match('/Opera/i', $client)) {

            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $client)) {

            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $client)) {

            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $client)) {

            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $client)) {

            $ub = "Opera";
        }

        return $ub;
    }


    public static function getUserIp()
    {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }


    public static function getUserCountry($ip)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=" . $ip);
        curl_setopt($ch, CURLOPT_HTTPHEADER,  array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        $country = json_decode($result)->geoplugin_countryName;
        return $country;
    }

    public static function getUserData($searchTerm)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $operatingSystem = static::getOperatingSystem($userAgent);
        $browser = static::getBrowser($userAgent);
        $ip = static::getUserIp();
        $country = static::getUserCountry($ip);
        $term = $searchTerm;

        static::insertDB($term);
        static::insertUserAnalytics($browser, $country, $operatingSystem);
    }

    public static function insertDB($term)

    {
        if (strlen($term) > 0 && !static::checkIfTermExists($term)) {
            $db = static::getDB();

            $stmt = 'INSERT INTO analytics (searchTerm,clicks,created_at)
            VALUES (:searchTerm,:clicks,:created_at)
            
            ';

            $query = $db->prepare($stmt);
            $query->bindParam(':searchTerm', $term);
            $query->bindValue(':created_at', date('Y-m-d'));
            $query->bindValue(':clicks', 1);
            $query->execute();
        } else {
            $clickValue = static::getClickValue($term);
            static::updateClickValue($term, $clickValue);
            static::deleteAnalytics($term, $clickValue);
        }
    }
    public static function checkIfTermExists($searchTerm)
    {
        $db = static::getDB();

        $stmt = 'SELECT * FROM analytics WHERE searchTerm = :searchTerm';

        $query = $db->prepare($stmt);

        $query->bindParam(':searchTerm', $searchTerm);

        $query->execute();


        return $query->rowCount() != 0;
    }

    public static function getClickValue($searchTerm)
    {
        $db = static::getDB();

        $stmt = 'SELECT * FROM analytics WHERE searchTerm = :searchTerm ORDER BY clicks DESC';

        $query = $db->prepare($stmt);

        $query->bindParam(':searchTerm', $searchTerm);

        $query->execute();


        return $query->fetch(PDO::FETCH_OBJ)->clicks;
    }

    public static function updateClickValue($searchTerm, $clickValue)
    {
        $time = date('Y-m-d');
        $db = static::getDB();

        $stmt = 'INSERT INTO analytics (searchTerm,clicks,created_at)
        VALUES (:searchTerm,:clicks,:created_at)
        
        ';

        $query = $db->prepare($stmt);

        $query->bindParam(':searchTerm', $searchTerm);
        $query->bindParam(':created_at', $time);
        $query->bindValue(':clicks', $clickValue + 1);
        $query->execute();
    }
    public static function deleteAnalytics($searchTerm, $clickValue)
    {
        $time = date('Y-m-d');
        $db = static::getDB();

        $stmt = 'DELETE FROM analytics WHERE clicks <= :clicks AND searchTerm = :searchTerm AND created_at = :created_at
        
    ';
        $query = $db->prepare($stmt);

        $query->bindParam(':searchTerm', $searchTerm);
        $query->bindParam(':created_at', $time);
        $query->bindValue(':clicks', $clickValue);
        $query->execute();
    }
    public static function getAnalytics($searchTerm)
    {
        $time = date('Y-m-d');
        $db = static::getDB();

        $stmt = 'SELECT * FROM analytics WHERE created_at BETWEEN (NOW() - INTERVAL 30 DAY) AND NOW() AND searchTerm = :searchTerm
        
        ';

        $query = $db->prepare($stmt);

        $query->bindParam(':searchTerm', $searchTerm);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public static function cleanData($data)
    {
        $obj = [];
        foreach ($data as $dataObj) {
            $date = explode(' ', $dataObj->created_at)[0];
            $obj[] =  [$date, $dataObj->clicks];
        }

        return $obj;
    }

    public static function insertUserAnalytics($browser,  $country, $operatingSystem)
    {
        if (!static::operatingSystemAndBrowserExist($operatingSystem, $browser)) {
            $stmt = 'INSERT INTO userAnalytics (browser,operating_system,country,created_date,visits)
    VALUES (:browser,:operating_system,:country,:created_date,:visits)
    ';
            $db = static::getDB();

            $query = $db->prepare($stmt);

            $query->bindParam(':browser', $browser);
            $query->bindParam(':operating_system', $operatingSystem);
            $query->bindParam(':country', $country);
            $query->bindValue(':visits', 1);
            $query->bindValue(':created_date', date('Y-m-d'));

            $query->execute();
        } else {
            $visits = static::getVisitsValue($operatingSystem, $browser);
            static::updateVisitsValue($operatingSystem, $browser, $country, $visits);
            static::deleteUserAnalytics($operatingSystem, $browser, $country, $visits);
        }
    }

    public static function operatingSystemAndBrowserExist($operatingSystem, $browser)
    {
        $db = static::getDB();
        $stmt = 'SELECT * FROM userAnalytics WHERE operating_system=:operating_system AND browser=:browser';

        $query = $db->prepare($stmt);
        $query->bindParam(':operating_system', $operatingSystem);
        $query->bindParam(':browser', $browser);
        $query->execute();
        return $query->rowCount() != 0;
    }

    public static function getVisitsValue($operatingSystem, $browser)
    {
        $db = static::getDB();
        $stmt = 'SELECT * FROM userAnalytics WHERE operating_system=:operating_system AND browser=:browser ORDER BY visits DESC';

        $query = $db->prepare($stmt);
        $query->bindParam(':operating_system', $operatingSystem);
        $query->bindParam(':browser', $browser);

        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ)->visits;
    }

    public static function updateVisitsValue($operatingSystem, $browser, $country, $visits)
    {
        $date = date('Y-m-d');
        $db = static::getDB();
        $stmt = 'INSERT INTO userAnalytics (browser,operating_system,country,created_date,visits)
        VALUES (:browser,:operating_system,:country,:created_date,:visits)';

        $query = $db->prepare($stmt);
        $query->bindParam(':operating_system', $operatingSystem);
        $query->bindParam(':browser', $browser);
        $query->bindParam(':country', $country);

        $query->bindValue(':created_date', $date);
        $query->bindValue(":visits", $visits + 1);
        $query->execute();
    }

    public static function deleteUserAnalytics($operatingSystem, $browser, $country, $visits)
    {
        $time = date('Y-m-d');
        $db = static::getDB();

        $stmt = 'DELETE FROM userAnalytics WHERE visits < :visits AND operating_system=:operating_system AND browser=:browser AND created_date = :created_date
        
    ';
        $query = $db->prepare($stmt);

        $query->bindParam(':browser', $browser);
        $query->bindParam(':operating_system', $operatingSystem);
        $query->bindValue(':visits', $visits + 1);
        $query->bindValue(':created_date', date('Y-m-d'));

        $query->execute();
    }

    public static function getUserAnalytics()
    {

        $db = static::getDB();

        $stmt = "SELECT * FROM userAnalytics WHERE created_date BETWEEN (NOW() - INTERVAL 30 DAY) AND NOW()";

        $query = $db->prepare($stmt);

        $query->execute();

        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    public static function showUsersInfo($data)
    {
        $userAnalytics = "<div>";
        foreach ($data as $userObj) {

            $userAnalytics .= "
                <div class = 'user-analytics-container'>
                    <p>operating system: " . $userObj->operating_system . "</p>
                    <p><strong>browser:</strong> " . $userObj->browser . "</p>
                    <p><strong>visits:</strong>: " . $userObj->visits . "</p>
                    <p><strong>country:</strong>: " . $userObj->country . "</p>
                    <p><strong>visit date:</strong>: " . explode(" ", $userObj->created_date)[0] . "</p>
                </div>
                ";
        }
        return $userAnalytics .= "</div>";
    }
}
