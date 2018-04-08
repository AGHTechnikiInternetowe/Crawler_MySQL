<?php
    // Constant
    const CRAWLER_DEPTH = 2;

// -------------------------------------- OPEN DATABASE CONNECTION ---------------------------------------
    // Database access data (individual for each person)
    $servername = "localhost";
    $username = "root";
    $password = ""; // <- IMPORTANT
    $dbname = "crawler";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        //echo "Connected successfully";
    }

// ---------------------------------------------- FUNCTIONS ----------------------------------------------
    function crawlPage($url, $depth = 5){
        // Variables
        $result = [];
        static $seen = array();
        if (isset($seen[$url]) || $depth === 0) {
            return;
        }
        $seen[$url] = true;

        // Create HTML object and get <a> tags
        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($url);
        $anchors = $dom->getElementsByTagName('a');

        // Extract urls from <a> tag
        foreach ($anchors as $element) {
            // Remove anchors
            $finalLink = explode("#", $element->getAttribute('href'));
            $link = $finalLink[0];

            // Add the protocol
            $adres = substr($link, 0, 7);
            $adresS = substr($link, 0, 8);

            $protocol = 'http://';
            $protocolS = 'https://';

            if($adres != $protocol && $adresS != $protocolS){
                //echo '<br>Brak protokolu<br><br>';
                $link = $url.$link;
            }

            array_push($result, $link);
        }
        $result = array_unique($result);

        return $result;
    }

    function getPageContent($url){

        // FUNCTION BODY
		$PageContent = file_get_contents($url);
        return htmlspecialchars($PageContent);
    }

// ---------------------------------------------- MAIN CODE ----------------------------------------------
    // Variables
    $pageCrawlerResult = [];

    // Input (for url) validation
    if(!empty($_GET['url'])){
        $url = $_GET['url'];
    }

    // Run Crawler
    if(isset($url)) {
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            echo 'Not a valid html!!!';
        } else {
            // Crawl urls
            $pageCrawlerResult = crawlPage($url, CRAWLER_DEPTH);
            $pageContentResult = getPageContent($url);

            // Save visited site (url + content)
            $sql = "INSERT INTO SitesViewed (site, content) VALUES ('$url', '$pageContentResult')";

            if ($conn->query($sql) === TRUE) {
                //echo "New site crawled successfully","<br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            // Save crawled urls into separate table
            foreach ($pageCrawlerResult as $link){
                $sql = "INSERT INTO SitesAwaiting (site) VALUES ('$link')";
                if ($conn->query($sql) === TRUE) {
                    //echo "New url to crawl added successfully";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }
    }

// -------------------------------------- CLOSE DATABASE CONNECTION --------------------------------------
    // Close connection with database
    $conn->close();
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
                <input type="text" class="search-input" name="url" value="<?php if(!empty($_GET['url'])){ echo $url; } ?>">
            </div>
            <div class="submit-container">
                <input class="submit" type="submit" value="Crawl!">
            </div>
        </form>
  	</div>
    <div class="result">
        <?php
            // Display crawler results in HTML
            foreach ($pageCrawlerResult as $index=>$href) {
                echo $result[$index] = '<a class="crawler-single-result" href="' . $href . '">' . $href . '</a>';
            }
        ?>
    </div>
  </body>
</html>
