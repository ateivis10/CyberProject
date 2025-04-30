<?php
    echo"<br>";
    $username = "root";
    $password = "";
    $database = "chat_app";

    // Create connection
    $mysqli = new mysqli("localhost", $username, $password, $database);

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }


?>
