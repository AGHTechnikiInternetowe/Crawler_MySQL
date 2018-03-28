<?php
    $pageCrawlerResult = '';

    function crawl_page($url, $depth = 5)
    {
        $result = '';
        static $seen = array();
        if (isset($seen[$url]) || $depth === 0) {
            return;
        }

        $seen[$url] = true;

        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($url);

        $anchors = $dom->getElementsByTagName('a');
        $hrefArray = [];

        foreach ($anchors as $element) {
            $href = explode("#",$element->getAttribute('href'));
            $href_final = $href[0];
            
            $adres1 = "http://";
			$adres2 = "https://";
			$start1 = substr($href_final,0,7);
			$start2 = substr($href_final,0,8);
			
			if ($adres1 != $start1 and $adres2 != $start2) {
			$href_final = $url.$href_final;
            $hrefArray[] = $href_final;
        }
        $hrefArray = array_unique($hrefArray);

        foreach ($hrefArray as $href) {
            $result .= '<a href="' . $href . '">' . $href . '</a>';
        }

        return $result;
    }
    $url = $_GET['url'];

    if(isset($url)) {
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            echo 'Not a valid html!!!';
        } else {
            $pageCrawlerResult = crawl_page($url, 2);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Crawler</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="header">Crawler</div>
  	<div class="search">
        <form action="" type="GET">
            <div class="search-container">
                <input type="text" class="search-input" name="url" value="<?php echo $url; ?>">
            </div>
            <div class="submit-container">
                <input class="submit" type="submit" value="Crawl!">
            </div>
        </form>
  	</div>
    <div class="result">
        <?php
            echo $pageCrawlerResult;
        ?>
    </div>
  </body>
</html>
