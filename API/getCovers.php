<?php
    error_reporting(E_ALL);
    error_reporting(-1);
    ini_set('error_reporting', E_ALL);

    require_once('../../scripts/dbconnect.php');
    require_once('functions.php');

    function getCovers() {
        global $mysqli;

        $sql = "SELECT * FROM `books`";

        $result = $mysqli->query($sql);

        echo "<div class='row'>";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $ISBN = $row['isbn13'];
                $url = "http://openlibrary.org/api/books?bibkeys=ISBN:$ISBN&jscmd=data&format=json"; 
                $client = curl_init($url);
                curl_setopt($client,CURLOPT_RETURNTRANSFER,true);
                $response = curl_exec($client);                
                $APIResult = json_decode($response, true);
                echo "<div class='col-2'>";
                $title = $row['title'];
                echo "<h3>$title</h3>";
                if (isset($APIResult["ISBN:$ISBN"]["identifiers"]["openlibrary"][0])) {
                    $openlibrary = $APIResult["ISBN:$ISBN"]["identifiers"]["openlibrary"][0];
                    echo "<img class='img img-100' src='http://covers.openlibrary.org/b/olid/$openlibrary-L.jpg'>";
                    echo "<p>openlibrary: $openlibrary</p>";
                }
                if (isset($APIResult["ISBN:$ISBN"]["identifiers"]["isbn_13"][0])) {
                    $isbn13 = $APIResult["ISBN:$ISBN"]["identifiers"]["isbn_13"][0];
                    echo "<p>isbn13: $isbn13</p>";
                }
                if (isset($APIResult["ISBN:$ISBN"]["identifiers"]["isbn_10"][0])) {
                    $isbn10 = $APIResult["ISBN:$ISBN"]["identifiers"]["isbn_10"][0];
                    echo "<p>isbn10: $isbn10</p>";
                }
                if (isset($APIResult["ISBN:$ISBN"]["identifiers"]["librarything"][0])) {
                    $librarything = $APIResult["ISBN:$ISBN"]["identifiers"]["librarything"][0];
                    echo "<p>librarything: $librarything</p>";
                }
                if (isset($APIResult["ISBN:$ISBN"]["identifiers"]["goodreads"][0])) {
                    $goodreads = $APIResult["ISBN:$ISBN"]["identifiers"]["goodreads"][0];
                    echo "<p>goodreads: $goodreads</p>";
                }
                echo "</div>";
            }
        }
        
        echo "</div>";
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <?php injectCSS(); ?>
</head>
<body>
    <?php renderHeader(); ?>
    <div class="container">
        <?php getCovers(); ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>