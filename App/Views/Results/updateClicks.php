<?php

use App\Config;
use App\Models\SearchModel;

$content = file_get_contents("php://input");

$decoded = json_decode($content);


$type = explode(':', $decoded)[0];
$id = explode(':', $decoded)[1];

if (isset($decoded)) {
     if ($type == 'site') {
          SearchModel::updateLinksDB($id);
     } else if ($type == 'img') {
          SearchModel::updateImagesDB($id);
     }
}
