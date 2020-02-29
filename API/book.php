<?php

    error_reporting(E_ALL);
    error_reporting(-1);
    ini_set('error_reporting', E_ALL);

    require_once('../../scripts/dbconnect.php');
    require_once('functions.php');

    function getBookInfo() {
        global $mysqli;

        $id = getvar('id');
        if ($id == null) {
            $id = 0;
        }


        $sql = "SELECT a.id,
                    a.title,
                    a.num_in_series,
                    a.cover_image,
                    b.id as `author_id`,
                    b.first_name as `authorFirst`,
                    b.last_name as `authorLast`,
                    c.title as `series`,
                    c.id as `seriesID`,
                    c.num_of_books,
                    a.isbn,
                    a.owned
                FROM `books` a
                    LEFT JOIN `authors` b
                        ON a.author_id = b.id
                    LEFT JOIN `series` c
                        ON a.series_id = c.id
                WHERE a.id = $id
                ORDER BY series, num_in_series";
        $result = $mysqli->query($sql);

        $format = getvar('format');
        if($format == 'json') {            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    array_push($data['results'], $row);
                }
            }
            return $data;
        } else {
            $row = $result->fetch_assoc();
            if ($row['owned'] == 0) {
                $owned = 'Unowned';
            } else {
                $owned = 'Owned';
            }
            echo "  <div class='container'>
                        <div class='row'>
                            <div class='col'>
                            <h1>Book Details</h1>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-4'>
                                <img class='img img-100' src='" . $row['cover_image'] . "'>
                            </div>
                            <div class='col-8'>
                                <h3>" . $row['title'] . "</h3>
                                <h4>By: <a href='http://192.168.33.10/BookDatabase/API/author.php?id=" . $row['author_id'] . "'>" . $row['authorFirst'] . " " . $row['authorLast'] . "</a></h4>
                                <p>Book " . $row['num_in_series'] . " of " . $row['num_of_books'] . " of <a href='http://192.168.33.10/BookDatabase/API/series.php?id=" . $row['seriesID'] . "'>" . $row['series'] . "</a></p>
                                <p>ISBN-13: " . $row['isbn'] . "</p>
                                <p>Owned: " . $owned . "</p>
                            </div>
                        </div>
                    </div>";
        }
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
        <?php getBookInfo(); ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
