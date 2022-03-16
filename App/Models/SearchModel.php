<?php


namespace App\Models;

use PDO;

class SearchModel extends \Core\Model
{

    public static function insertLinksDB($url, $description, $title, $keywords)
    {
        $db = static::getDB();
        $stmt = 'INSERT INTO sites(url,title,description,keywords)
        VALUES (:url,:title,:description,:keywords)
        ';

        $query = $db->prepare($stmt);

        $query->bindParam(':url', $url);
        $query->bindParam(':title', $title);
        $query->bindParam(':description', $description);
        $query->bindParam(':keywords', $keywords);


        return $query->execute();
    }

    public static function checkIfLinkExists($url)
    {
        $db = static::getDB();
        $stmt = 'SELECT * FROM sites WHERE url = :url';
        $query = $db->prepare($stmt);
        $query->bindParam(':url', $url);
        $query->execute();



        return $query->rowCount() != 0;
    }

    public static function getResults($term, $page = 1, $pageSize = 20)
    {
        $fromlimit = ($page - 1) * $pageSize;
        $db = static::getDB();
        $stmt = "SELECT * FROM sites WHERE title LIKE :term OR url LIKE :term OR keywords LIKE :term OR description LIKE :term
        ORDER BY clicks DESC
        LIMIT :fromLimit,:pageSize
        ";

        $query = $db->prepare($stmt);
        $searchTerm = "%" . $term . "%";
        $query->bindParam(':term', $searchTerm);
        $query->bindParam(':fromLimit', $fromlimit, PDO::PARAM_INT);

        $query->bindParam(':pageSize', $pageSize, PDO::PARAM_INT);
        $query->execute();

        $resultsHtml = "<div class='site-container'>";
        while ($data = $query->fetch(PDO::FETCH_OBJ)) {
            $id = $data->id;

            $url = wordwrap($data->url, 180, '<br/>\n');
            $title = $data->title;
            $description = wordwrap($data->description, 130, '<br/>\n');


            $resultsHtml .= "<div class='results-container'>
            <h3 class='title'>
                <a class='result' href=" . $url . " data-linkId= " . $id . ">" . $title . "</a>
            </h3>
            <span class='url'>" . $url . "</span>
            <span class='description'>" . $description . "</span>
            </div>";
        }
        return $resultsHtml . "</div>";
    }

    public static function getNumResults($term)
    {
        $db = static::getDB();
        $stmt = "SELECT COUNT(*) as total FROM sites WHERE title LIKE :term OR url LIKE :term OR keywords LIKE :term OR description LIKE :term";

        $query = $db->prepare($stmt);
        $searchTerm = "%" . $term . "%";
        $query->bindParam(':term', $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_OBJ);
        return $row->total;
    }

    public static function updateLinksDB($id)
    {
        $db = static::getDB();
        $stmt = 'UPDATE sites SET clicks = clicks + 1 WHERE id=:id';

        $query = $db->prepare($stmt);
        $query->bindParam(":id", $id);
        $query->execute();
    }
    public static function updateImagesDB($id)
    {
        $db = static::getDB();
        $stmt = 'UPDATE images SET clicks = clicks + 1 WHERE id=:id';

        $query = $db->prepare($stmt);
        $query->bindParam(":id", $id);
        $query->execute();
    }
}
