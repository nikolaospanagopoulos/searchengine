<?php

namespace App;

use App\Models\ImageSearchModel;
use App\Models\SearchModel;
use DOMDocument;

class Crawler
{
    private  $document;

    public $crawledImages = [];
    public $crawled = [];
    public  function getAll($url, $depth = 1)
    {
        static $seen = array();
        if ($depth === 0) {
            return;
        }

        $seen[$url] = true;
        $options = array(
            'http' => array('method' => "GET", "header" => "User-Agent: doodleBot/0.1\n")
        );

        $context = stream_context_create($options);
        $this->document = new DOMDocument();
        libxml_use_internal_errors(true);
        @$this->document->loadHTML(file_get_contents($url, false, $context));
        libxml_clear_errors();

        $anchors = $this->getLinks();
        foreach ($anchors as $element) {
            $href = $element->getAttribute('href');
            if (strpos($href, '#') !== false) {
                continue;
            } else if (substr($href, 0, 11) == 'javascript:') {
                continue;
            }

            $href = $this->createLinks($url, $href);

            if (!in_array($href, $this->crawled)) {
                $this->crawled[] = $href;

                $this->getDetails($href);

                $this->getImageList($href);
            }
            $this->getAll($href, $depth - 1);
        }
    }

    private function getLinks()
    {
        return $this->document->getElementsByTagName('a');
    }

    private function getImageTags()
    {
        return $this->document->getElementsByTagName('img');
    }
    private function createLinks($url, $src)
    {

        $scheme = parse_url($url)['scheme'];
        $host = parse_url($url)['host'];
        $path = parse_url($url)['path'] ?? '';

        if (substr($src, 0, 2) == '//') {
            $src = $scheme . ":" . $src;
        } else if (substr($src, 0, 1) == '/') {
            $src = $scheme . "://" . $host . $src;
        } else if (substr($src, 0, 2) == './') {
            $src = $scheme . "://" . $host . dirname($path) . substr($src, 1);
        } else if (substr($src, 0, 3) == '../') {
            $src = $scheme . "://" . $host . "/" . $src;
        } else if (substr($src, 0, 4) != 'http') {
            $src = $scheme . "://" . $host . "/" . $src;
        }
        return $src;
    }

    private function getDetails($url)
    {
        $titleArray = $this->getTitleTags();
        $title = $titleArray[0]->nodeValue;
        if (sizeof($titleArray) == 0 && $titleArray[0] == NULL) {
            return;
        }
        $title = str_replace('\n', "", $title);
        if (strlen($title) == 0) {
            return;
        }

        $metasArray = $this->getMetaKeywords();

        $metaLength = count($metasArray);
        $description = '';
        $keywords = '';
        for ($i = 0; $i < $metaLength; $i++) {
            // echo $metasArray[$i];
            if ($metasArray[$i]->getAttribute('name') == 'description') {
                $description = $metasArray[$i]->getAttribute('content');
            }

            if ($metasArray[$i]->getAttribute('name') == 'keywords') {
                $keywords = $metasArray[$i]->getAttribute('content');
            }
            $description = str_replace("\n", "", $description);
            $keywords = str_replace("\n", "", $keywords);

            if (!SearchModel::checkIfLinkExists($url)) {
                SearchModel::insertLinksDB($url, $description, $title, $keywords);
            }

            $this->getImageList($url);
        }
    }

    private function getTitleTags()
    {
        return $this->document->getElementsByTagName('title');
    }

    private function getMetaKeywords()
    {
        return $this->document->getElementsByTagName('meta');
    }


    private function getImageList($url)
    {
        $imageList = $this->getImageTags();

        $imageListLength = count($imageList);

        for ($i = 0; $i < $imageListLength; $i++) {
            $src = $imageList[$i]->getAttribute('src');
            $alt = $imageList[$i]->getAttribute('alt');
            $title = $imageList[$i]->getAttribute('title');


            if (!$title && !$src) {
                continue;
            }
            $src = $this->createLinks($url, $src);
            if (!in_array($src, $this->crawledImages)) {
                $this->crawledImages[] = $src;


                if (!ImageSearchModel::checkIfImageExists($src)) {
                    ImageSearchModel::insertImagesDB($url, $src, $alt, $title);
                }
            }
        }
    }
}
