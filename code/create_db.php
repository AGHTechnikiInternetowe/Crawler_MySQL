<?php
// University database access data
//$servername = "localhost";
//$username = "root";
//$password = "mysql";

// User (home) database access data
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h3 style='margin: 0;'>Etapy tworzenia bazy danych:</h3><ul id='database-creation-part-list' style='margin: 10px 0 0 0; list-style-type:decimal;'>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS Crawler";
if ($conn->query($sql) === TRUE) {
    echo "<li>Database created successfully</li>";
} else {
    echo "<li>Error creating database: " . $conn->error . "</li>";
}

$conn->select_db("Crawler");

// sql to create table
$sql = "CREATE TABLE IF NOT EXISTS SitesViewed (
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	site VARCHAR(200),
	content TEXT,
	date TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "<li>Table SitesViewed created successfully</li>";
} else {
    echo "<li>Error creating table: " . mysqli_error($conn). "</li>";
}

// sql to create table
$sql = "CREATE TABLE IF NOT EXISTS SitesAwaiting (
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	site VARCHAR(200),
	date TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "<li>Table SitesAwaiting created successfully</li>";
} else {
    echo "<li>Error creating table: " . mysqli_error($conn). "</li>";
}

$result = mysqli_query($conn, "SHOW COLUMNS FROM `SitesViewed` LIKE 'content'");
$exists = (mysqli_num_rows($result)) ? TRUE:FALSE;

if (!$exists) {
    // sql to ALTER table
    $sql = "ALTER TABLE SitesViewed ADD content TEXT after site";

    if (mysqli_query($conn, $sql)) {
        echo "ALTER TABLE SitesViewed successfully";
    } else {
        echo "<li>Error creating table: " . mysqli_error($conn). "</li>";
    }
    
    // sql to ALTER table
    $sql = "ALTER TABLE SitesViewed MODIFY site VARCHAR(2048)";
    if (mysqli_query($conn, $sql)) {
        echo "ALTER TABLE SitesViewed successfully";
    } else {
        echo "<li>Error creating table: " . mysqli_error($conn). "</li>";
    }
}

$conn->close();

echo "</ul>";

?>