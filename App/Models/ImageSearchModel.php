<?php

namespace App\Models;

use PDO;

class ImageSearchModel extends \Core\Model
{

    public static function getNumResults($term)
    {
        $db = static::getDB();
        $stmt = "SELECT COUNT(*) as total FROM images WHERE title LIKE :term OR alt LIKE :term OR siteUrl LIKE :term";

        $query = $db->prepare($stmt);
        $searchTerm = "%" . $term . "%";
        $query->bindParam(':term', $searchTerm);
        $query->execute();


        $row = $query->fetch(PDO::FETCH_OBJ);
        return $row->total;
    }
    public static function checkIfImageExists($imageUrl)
    {
        $db = static::getDB();

        $stmt = 'SELECT * FROM images WHERE imageUrl = :imageUrl';

        $query = $db->prepare($stmt);

        $query->bindParam(':imageUrl', $imageUrl);

        $query->execute();


        return $query->rowCount() != 0;
    }


    public static function insertImagesDB($siteUrl, $imageUrl, $alt, $title)
    {
        $db = static::getDB();

        $stmt = 'INSERT INTO images(siteUrl,imageUrl,alt,title)
        VALUES(:siteUrl,:imageUrl,:alt,:title)
        ';

        $query = $db->prepare($stmt);

        $query->bindParam(':imageUrl', $imageUrl);
        $query->bindParam(':siteUrl', $siteUrl);
        $query->bindParam(':alt', $alt);
        $query->bindParam(':title', $title);
        $query->execute();
    }

    public static function getResultsHtml($page, $pageSize, $term)
    {
        $db = static::getDB();
        $fromLimit = ($page - 1) * $pageSize;

        $stmt = 'SELECT * FROM images WHERE (title LIKE :term OR alt LIKE :term OR siteUrl LIKE :term)
        ORDER BY clicks DESC
        LIMIT :fromLimit,:pageSize
        ';

        $query = $db->prepare($stmt);
        $searchTerm = "%" . $term . "%";

        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);

        $query->execute();



        $resultsHTML = "<div class='imageResults'>";

        while ($data = $query->fetch(PDO::FETCH_OBJ)) {
            $id = $data->id;
            $imageUrl = $data->imageUrl;
            $siteUrl = $data->siteUrl;
            $title = $data->title;
            $alt = $data->alt;

            if ($title) {
                $displayText = $title;
            } else if ($alt) {
                $displayText = $alt;
            } else {
                $displayText = $imageUrl;
            }

            $resultsHTML .= "<div class='grid-item'>
                
                    <img src=" . $imageUrl . " data-linkId='$id' data-url = '$imageUrl' class='img-result'/>
                    <span class='details'>" . $displayText . "</span>
                
                </div>
            ";
        }
        return $resultsHTML .= "</div>";
    }
}
