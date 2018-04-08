<?php
    $pageCrawlerResult = [];
	$crawlDepth = 2;
	//require 'create_db.php'; // display information about creating tables on the top of website, so I will copy settings from create_db.php (the information won't be display)
	$servername = "localhost";
	$username = "root";
	$password = "";
	// Create connection
	$conn = new mysqli($servername, $username, $password);
	// selecting database to adding query
	$conn->select_db("Crawler");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
    function crawl_page($url, $depth = 5)
    {
		$result = [];
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
			// Push final link to array
            $result[] = $link;
			//array_push($result,$link);
        }
        $result = array_unique($result);
		// print_r($result);

        return $result;
    }

    if(!empty($_GET['url'])){
        $url = $_GET['url'];
    }

    if(isset($url)) {
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            echo 'Not a valid html!!!';
        } else {
            $pageCrawlerResult = crawl_page($url, $crawlDepth);
			
			// save name of url address
			$sql = "INSERT INTO SitesAwaiting (site) VALUES ('$url')";
			//if (mysqli_query($conn, $sql)) {
			if ($conn->query($sql) === TRUE) {
				//echo "URL ADDRESS added successfully to database".'<br>';
			} else {
				echo "Error creating query " . mysqli_error($conn).'<br>';
			}

			// save links from url
			foreach ($pageCrawlerResult as $link){
				$sql = "INSERT INTO SitesViewed (site, content) VALUES ('$url', '$link')";
				//if (mysqli_query($conn, $sql)) {
				if ($conn->query($sql) === TRUE) {
					//echo "Query insert successfully".'<br>';
				} else {
					echo "Error creating query " . mysqli_error($conn).'<br>';
				}
			}
        }
    }
	// close connection to database 
	$conn -> close();
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
			foreach ($pageCrawlerResult as $href) {
				echo '<a href = "'.$href.'">'.$href.'</a>';
			}
        ?>
    </div>
  </body>
</html>
