<?php

    error_reporting(E_ALL);
    error_reporting(-1);
    ini_set('error_reporting', E_ALL);

    require_once('../../scripts/dbconnect.php');
    require_once('functions.php');

    function getSeriesInfo() {
        global $mysqli;

        $id = getvar('id');
        if ($id == null) {
            $id = 0;
        }

        $sql = "SELECT * FROM `series` WHERE id=$id";

        $result = $mysqli->query($sql);

        $format = getvar('format');
        if($format == 'json') {          
            $row = $result->fetch_assoc();  
            if ($result->num_rows > 0) {
                array_push($data['results'], $row);
            }
            return $data;
        } else {
            $row = $result->fetch_assoc();
            echo "  <div class='row'>
                        <div class='col-9'>
                            <h2>" . $row['title'] . "</h2>
                        </div>
                        <div class='col-3'>
                            <a href='#' class='btn btn-block btn-primary'>Edit Series</a>
                        </div>
                    </div>
                    <div class='row'>";

            $sql2 = "SELECT * FROM `series` WHERE id=$id";
            $numOfBooksResults = $mysqli->query($sql2);
            $numOfBooksRow = $numOfBooksResults->fetch_assoc();
            $numOfBooks = $numOfBooksRow['num_of_books'];

            $sql3 = "SELECT * FROM `books` WHERE series_id=$id ORDER BY num_in_series";
            $results = $mysqli->query($sql3);
            while($rows = $results->fetch_assoc()) {
                if ($results->num_rows > 0) {
                    $ownedCSS = '';
                    if ($rows['owned'] == 0) {
                        $ownedCSS = ' unowned';
                    }
                    echo "<div class='col-2 mb-2'>
                        <a href='http://192.168.33.10/BookDatabase/API/book.php?id=" . $rows['id'] . "'>
                            <img class='img img-100" . $ownedCSS . "' src='" . $rows['cover_image'] . "'><p class='text-center metal linear'>" . $rows['title'] . "<br>(" . $rows['num_in_series'] . " of " . $numOfBooks . ")</p>" .
                        "</a>
                    </div>";
                }
            }
                                
            echo "</div>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Series Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <?php injectCSS(); ?>
</head>

<body>
    <?php renderHeader(); ?>
    <div class="container">
        <?php getSeriesInfo(); ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>